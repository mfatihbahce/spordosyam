<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\School;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IyzicoService
{
    private $apiKey;
    private $secretKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.iyzico.api_key');
        $this->secretKey = config('services.iyzico.secret_key');
        $this->baseUrl = config('services.iyzico.base_url', 'https://api.iyzipay.com');
    }

    /**
     * Pazaryeri ödemesi oluştur (Sub-merchant key ile)
     * Iyzico IYZWSv2 imzası kullanılır (x-iyzi-rnd zorunlu).
     * Bu ödeme türünde Iyzico otomatik olarak komisyonu keser ve okula transfer eder.
     */
    public function createMarketplacePayment($paymentData, $subMerchantKey, $subMerchantPrice)
    {
        try {
            $request = [
                'locale' => 'tr',
                'conversationId' => $paymentData['conversation_id'],
                'price' => number_format($paymentData['amount'], 2, '.', ''),
                'paidPrice' => number_format($paymentData['amount'], 2, '.', ''),
                'currency' => 'TRY',
                'basketId' => $paymentData['basket_id'],
                'paymentCard' => [
                    'cardHolderName' => $paymentData['card_holder_name'],
                    'cardNumber' => $paymentData['card_number'],
                    'expireMonth' => $paymentData['expire_month'],
                    'expireYear' => $paymentData['expire_year'],
                    'cvc' => $paymentData['cvc'],
                    'registerCard' => 0,
                ],
                'buyer' => [
                    'id' => (string) $paymentData['buyer_id'],
                    'name' => $paymentData['buyer_name'],
                    'surname' => $paymentData['buyer_surname'],
                    'gsmNumber' => $paymentData['buyer_phone'],
                    'email' => $paymentData['buyer_email'],
                    'identityNumber' => $paymentData['buyer_identity_number'] ?? '',
                    'lastLoginDate' => date('Y-m-d H:i:s'),
                    'registrationDate' => date('Y-m-d H:i:s'),
                    'registrationAddress' => $paymentData['buyer_address'] ?? '',
                    'ip' => request()->ip(),
                    'city' => $paymentData['buyer_city'] ?? 'Istanbul',
                    'country' => 'Turkey',
                    'zipCode' => $paymentData['buyer_zip_code'] ?? '',
                ],
                'shippingAddress' => [
                    'contactName' => $paymentData['buyer_name'] . ' ' . $paymentData['buyer_surname'],
                    'city' => $paymentData['buyer_city'] ?? 'Istanbul',
                    'country' => 'Turkey',
                    'address' => $paymentData['buyer_address'] ?? '',
                    'zipCode' => $paymentData['buyer_zip_code'] ?? '',
                ],
                'billingAddress' => [
                    'contactName' => $paymentData['buyer_name'] . ' ' . $paymentData['buyer_surname'],
                    'city' => $paymentData['buyer_city'] ?? 'Istanbul',
                    'country' => 'Turkey',
                    'address' => $paymentData['buyer_address'] ?? '',
                    'zipCode' => $paymentData['buyer_zip_code'] ?? '',
                ],
                'basketItems' => [
                    [
                        'id' => $paymentData['basket_item_id'],
                        'name' => $paymentData['basket_item_name'],
                        'category1' => 'Aidat',
                        'category2' => 'Öğrenci Aidatı',
                        'itemType' => 'VIRTUAL',
                        'price' => number_format($paymentData['amount'], 2, '.', ''),
                        'subMerchantKey' => $subMerchantKey,
                        'subMerchantPrice' => number_format($subMerchantPrice, 2, '.', ''),
                    ],
                ],
            ];

            $uriPath = '/payment/auth';
            $endpoint = $this->baseUrl . $uriPath;
            $jsonRequest = json_encode($request, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $jsonRequest = trim($jsonRequest);

            $randomKey = (string) (time() * 1000) . bin2hex(random_bytes(4));
            $payload = $randomKey . $uriPath . $jsonRequest;
            $signatureHex = hash_hmac('sha256', $payload, $this->secretKey, false);
            $authString = 'apiKey:' . $this->apiKey . '&randomKey:' . $randomKey . '&signature:' . $signatureHex;
            $authorizationHeader = 'IYZWSv2 ' . base64_encode($authString);

            $client = new \GuzzleHttp\Client(['timeout' => 30, 'verify' => true]);

            $response = $client->post($endpoint, [
                'headers' => [
                    'Authorization' => $authorizationHeader,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'x-iyzi-rnd' => $randomKey,
                ],
                'body' => $jsonRequest,
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $result = json_decode($body, true);

            if ($result && ($result['status'] ?? '') === 'success') {
                return [
                    'success' => true,
                    'payment_id' => $result['paymentId'],
                    'conversation_id' => $result['conversationId'],
                    'data' => $result,
                ];
            }

            return [
                'success' => false,
                'error' => $result['errorMessage'] ?? 'Ödeme işlemi başarısız oldu',
                'data' => $result,
            ];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse();
            $body = $response ? $response->getBody()->getContents() : '';
            $result = $body ? json_decode($body, true) : null;
            Log::error('Iyzico Marketplace Payment RequestException', [
                'message' => $e->getMessage(),
                'body' => $body,
            ]);
            return [
                'success' => false,
                'error' => $result['errorMessage'] ?? $e->getMessage(),
                'data' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Iyzico Marketplace Payment Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Ödeme işlemi sırasında bir hata oluştu: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Sub-merchant (Alt işyeri) oluştur
     * Iyzico dokümantasyonuna göre: https://docs.iyzico.com/urunler/pazaryeri/pazaryeri-entegrasyonu/alt-uye-olusturma
     */
    public function createSubMerchant($school, $bankAccount)
    {
        try {
            // API key ve secret key kontrolü
            if (empty($this->apiKey) || empty($this->secretKey)) {
                return [
                    'success' => false,
                    'error' => 'Iyzico API anahtarları yapılandırılmamış. Lütfen Superadmin panelinden ödeme ayarlarını kontrol edin.',
                ];
            }

            // Sub-merchant tipi kontrolü
            if (!$bankAccount->sub_merchant_type) {
                return [
                    'success' => false,
                    'error' => 'Alt işyeri tipi seçilmemiş. Lütfen banka hesabı bilgilerini düzenleyin ve alt işyeri tipini seçin.',
                ];
            }

            // IBAN formatını temizle (boşlukları kaldır) - formda zorunlu, her zaman gönderilir
            $iban = !empty($bankAccount->iban) ? str_replace(' ', '', strtoupper($bankAccount->iban)) : null;

            // Telefon numarasını temizle ve formatla
            // ÖNCE bank_account'tan al, yoksa school'dan al
            $phoneRaw = $bankAccount->gsm_number ?? $school->phone ?? null;
            if (empty($phoneRaw)) {
                return [
                    'success' => false,
                    'error' => 'Telefon numarası (GSM) bilgisi zorunludur. Lütfen banka hesabı bilgilerini düzenleyin.',
                ];
            }
            $phone = preg_replace('/[^0-9]/', '', $phoneRaw);
            if (strlen($phone) < 10) {
                return [
                    'success' => false,
                    'error' => 'Geçerli bir telefon numarası gerekli. Telefon numarası en az 10 haneli olmalıdır.',
                ];
            }
            // Iyzico +90 formatı bekliyor (örn: +905551234567)
            if (substr($phone, 0, 2) !== '90') {
                $phone = '90' . ltrim($phone, '0');
            }
            $phone = '+' . $phone;

            // Email formatı kontrolü
            // ÖNCE bank_account'tan al, yoksa school'dan al
            $email = $bankAccount->email ?? $school->email ?? null;
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'error' => 'Geçerli bir e-posta adresi gerekli. Lütfen banka hesabı bilgilerini düzenleyin.',
                ];
            }
            $email = trim($email);
            
            // Address alanı kontrolü - minimum 10 karakter olmalı
            // ÖNCE bank_account'tan al, yoksa school'dan al
            $address = trim($bankAccount->address ?? $school->address ?? '');
            if (empty($address) || strlen($address) < 10) {
                return [
                    'success' => false,
                    'error' => 'Adres bilgisi zorunludur ve en az 10 karakter olmalıdır. Lütfen banka hesabı bilgilerini düzenleyin.',
                ];
            }
            
            // Temel request alanları - Iyzico dokümantasyonuna göre EXACT sıralama
            // Iyzico dokümantasyonuna göre PERSONAL tipi için zorunlu alanlar:
            // locale, conversationId, subMerchantExternalId, subMerchantType, email, gsmNumber, address, contactName, contactSurname, identityNumber
            // Opsiyonel: name, iban, currency
            $request = [];
            
            // Iyzico destek ekibinin önerdiği format ve sıralama
            // ÖNEMLİ: conversationId ve subMerchantExternalId'de "_" kullanılmamalı
            // Iyzico örnek formatına göre sıralama:
            // locale, conversationId, name, email, gsmNumber, address, iban, contactName, contactSurname, currency, subMerchantExternalId, identityNumber, subMerchantType
            
            $request['locale'] = 'tr';
            // conversationId'de "_" kullanmıyoruz (Iyzico önerisi)
            $request['conversationId'] = 'submerchant' . $school->id . time();
            
            // Sub-merchant tipine göre zorunlu alanları ekle
            switch ($bankAccount->sub_merchant_type) {
                case 'PERSONAL':
                    // Bireysel için zorunlu: identityNumber, contactName, contactSurname
                    if (!$bankAccount->identity_number || !$bankAccount->contact_name || !$bankAccount->contact_surname) {
                        return [
                            'success' => false,
                            'error' => 'Bireysel alt işyeri için TC Kimlik No, İletişim Adı ve Soyadı zorunludur.',
                        ];
                    }
                    
                    // Iyzico örnek formatına göre sıralama
                    // name parametresi ekleniyor (Iyzico örnekte var)
                    if ($school->name) {
                        $request['name'] = trim($school->name);
                    } else {
                        // Okul adı yoksa contactName ve contactSurname'den oluştur
                        $request['name'] = trim($bankAccount->contact_name) . ' ' . trim($bankAccount->contact_surname);
                    }
                    
                    $request['email'] = $email;
                    $request['gsmNumber'] = $phone;
                    $request['address'] = $address;
                    
                    if ($iban) {
                        $request['iban'] = $iban;
                    }
                    
                    $request['contactName'] = trim($bankAccount->contact_name);
                    $request['contactSurname'] = trim($bankAccount->contact_surname);
                    $request['currency'] = 'TRY';
                    // subMerchantExternalId'de "_" kullanmıyoruz (Iyzico önerisi)
                    $request['subMerchantExternalId'] = 'SCHOOL' . $school->id;
                    $request['identityNumber'] = trim($bankAccount->identity_number);
                    $request['subMerchantType'] = $bankAccount->sub_merchant_type;
                    break;

                case 'PRIVATE_COMPANY':
                    // Şahıs Şirketi için zorunlu: taxOffice, legalCompanyTitle
                    if (!$bankAccount->tax_office || !$bankAccount->legal_company_title) {
                        return [
                            'success' => false,
                            'error' => 'Şahıs Şirketi için Vergi Dairesi ve Yasal Şirket Unvanı zorunludur.',
                        ];
                    }
                    
                    // Iyzico örnek formatına göre sıralama
                    if ($school->name) {
                        $request['name'] = trim($school->name);
                    } else {
                        $request['name'] = trim($bankAccount->legal_company_title);
                    }
                    
                    $request['email'] = $email;
                    $request['gsmNumber'] = $phone;
                    $request['address'] = $address;
                    
                    if ($iban) {
                        $request['iban'] = $iban;
                    }
                    
                    $request['taxOffice'] = trim($bankAccount->tax_office);
                    if ($bankAccount->tax_number) {
                        $request['taxNumber'] = trim($bankAccount->tax_number);
                    }
                    $request['legalCompanyTitle'] = trim($bankAccount->legal_company_title);
                    $request['currency'] = 'TRY';
                    // subMerchantExternalId'de "_" kullanmıyoruz (Iyzico önerisi)
                    $request['subMerchantExternalId'] = 'SCHOOL' . $school->id;
                    $request['subMerchantType'] = $bankAccount->sub_merchant_type;
                    break;

                case 'LIMITED_OR_JOINT_STOCK_COMPANY':
                    // Limited/Anonim Şirket için zorunlu: taxOffice, taxNumber, legalCompanyTitle
                    if (!$bankAccount->tax_office || !$bankAccount->tax_number || !$bankAccount->legal_company_title) {
                        return [
                            'success' => false,
                            'error' => 'Limited/Anonim Şirket için Vergi Dairesi, Vergi Numarası ve Yasal Şirket Unvanı zorunludur.',
                        ];
                    }
                    
                    // Iyzico örnek formatına göre sıralama
                    if ($school->name) {
                        $request['name'] = trim($school->name);
                    } else {
                        $request['name'] = trim($bankAccount->legal_company_title);
                    }
                    
                    $request['email'] = $email;
                    $request['gsmNumber'] = $phone;
                    $request['address'] = $address;
                    
                    if ($iban) {
                        $request['iban'] = $iban;
                    }
                    
                    $request['taxOffice'] = trim($bankAccount->tax_office);
                    $request['taxNumber'] = trim($bankAccount->tax_number);
                    $request['legalCompanyTitle'] = trim($bankAccount->legal_company_title);
                    $request['currency'] = 'TRY';
                    // subMerchantExternalId'de "_" kullanmıyoruz (Iyzico önerisi)
                    $request['subMerchantExternalId'] = 'SCHOOL' . $school->id;
                    $request['subMerchantType'] = $bankAccount->sub_merchant_type;
                    break;

                default:
                    return [
                        'success' => false,
                        'error' => 'Geçersiz alt işyeri tipi.',
                    ];
            }

            // Iyzico API endpoint - path imza hesabında kullanılacak
            $uriPath = '/onboarding/submerchant';
            $endpoint = $this->baseUrl . $uriPath;
            
            // Request body'yi JSON formatına çevir
            // ÖNEMLİ: Iyzico API'si için alanların sırası önemli olabilir
            // JSON_UNESCAPED_UNICODE: Türkçe karakterler için
            // JSON_UNESCAPED_SLASHES: Slash karakterleri için
            // JSON_PRETTY_PRINT kullanmıyoruz çünkü signature için tam format önemli
            $jsonRequest = json_encode($request, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // JSON encoding hatası kontrolü
            if ($jsonRequest === false) {
                $jsonError = json_last_error_msg();
                Log::error('Iyzico JSON Encoding Error', [
                    'error' => $jsonError,
                    'request' => $request,
                ]);
                return [
                    'success' => false,
                    'error' => 'Request body JSON formatına çevrilemedi: ' . $jsonError,
                ];
            }
            
            // JSON string'deki boşlukları temizle (signature için önemli)
            // Iyzico signature kontrolü için JSON string'in tam formatı önemli
            $jsonRequest = trim($jsonRequest);
            
            // Detaylı loglama - Iyzico API'sine gönderilen request'i tam olarak logla
            Log::info('Iyzico Sub-Merchant Request (Full Details)', [
                'school_id' => $school->id,
                'bank_account_id' => $bankAccount->id,
                'endpoint' => $endpoint,
                'base_url' => $this->baseUrl,
                'sub_merchant_type' => $bankAccount->sub_merchant_type,
                'request_array' => $request,
                'request_json' => $jsonRequest,
                'request_json_length' => strlen($jsonRequest),
                'api_key_preview' => substr($this->apiKey, 0, 15) . '...',
                'api_key_length' => strlen($this->apiKey),
                'has_secret_key' => !empty($this->secretKey),
                'phone_formatted' => $phone,
                'email' => $email,
                'address_length' => strlen($address),
                'iban' => $iban,
            ]);

            // Iyzico IYZWSv2 imza formatı (BIN/Installment ile aynı - HMACSHA256 Auth dokümantasyonu)
            // payload = randomKey + uri_path + request.body
            // signature = HMAC-SHA256(payload, secretKey) -> HEX
            // auth = base64("apiKey:"+apiKey+"&randomKey:"+randomKey+"&signature:"+signature)
            $randomKey = (string) (time() * 1000) . bin2hex(random_bytes(4));
            $payload = $randomKey . $uriPath . $jsonRequest;
            $signatureHex = hash_hmac('sha256', $payload, $this->secretKey, false);
            $authString = 'apiKey:' . $this->apiKey . '&randomKey:' . $randomKey . '&signature:' . $signatureHex;
            $authorizationHeader = 'IYZWSv2 ' . base64_encode($authString);

            Log::info('Iyzico Sub-Merchant Request (IYZWSv2 - uri_path + body, hex signature)', [
                'endpoint' => $endpoint,
                'uri_path' => $uriPath,
                'authorization_method' => 'IYZWSv2',
                'request_body_length' => strlen($jsonRequest),
            ]);

            try {
                $client = new \GuzzleHttp\Client([
                    'timeout' => 30,
                    'verify' => true,
                ]);

                $response = $client->post($endpoint, [
                    'headers' => [
                        'Authorization' => $authorizationHeader,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'x-iyzi-rnd' => $randomKey,
                    ],
                    'body' => $jsonRequest,
                ]);
                
                $statusCode = $response->getStatusCode();
                $body = $response->getBody()->getContents();
                $result = json_decode($body, true);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $response = $e->getResponse();
                if ($response) {
                    $statusCode = $response->getStatusCode();
                    $body = $response->getBody()->getContents();
                    $result = json_decode($body, true);
                } else {
                    Log::error('Iyzico API Request Exception (No Response)', [
                        'message' => $e->getMessage(),
                        'endpoint' => $endpoint,
                    ]);
                    return [
                        'success' => false,
                        'error' => 'Iyzico API\'ye bağlanılamadı: ' . $e->getMessage(),
                    ];
                }
            }

            // Response handling - Guzzle response'dan veri alıyoruz
            // Yukarıdaki try-catch bloğunda zaten $statusCode, $body ve $result alındı

            Log::info('Iyzico Sub-Merchant Response', [
                'status_code' => $statusCode,
                'response' => $result,
                'raw_body' => $body,
            ]);

            // Eğer JSON parse edilemediyse
            if ($result === null && !empty($body)) {
                // HTML veya başka bir format dönmüş olabilir
                if (strpos($body, '<html') !== false || strpos($body, '<!DOCTYPE') !== false) {
                    Log::error('Iyzico API returned HTML instead of JSON', [
                        'endpoint' => $endpoint,
                        'base_url' => $this->baseUrl,
                        'api_key_set' => !empty($this->apiKey),
                        'body_preview' => substr($body, 0, 1000),
                    ]);
                    
                    $errorMsg = 'Iyzico API\'den HTML yanıt alındı. ';
                    $errorMsg .= 'Bu genellikle şu durumlarda olur: ';
                    $errorMsg .= '1) API anahtarları yanlış veya boş, ';
                    $errorMsg .= '2) Base URL yanlış (Test: https://sandbox-api.iyzipay.com, Canlı: https://api.iyzipay.com), ';
                    $errorMsg .= '3) API anahtarları test ortamı için ama canlı URL kullanılıyor (veya tam tersi). ';
                    $errorMsg .= 'Lütfen Superadmin panelinden Ödeme Ayarları\'nı kontrol edin.';
                    
                    return [
                        'success' => false,
                        'error' => $errorMsg,
                        'data' => [
                            'endpoint' => $endpoint,
                            'base_url' => $this->baseUrl,
                            'raw_response_preview' => substr($body, 0, 500),
                        ],
                    ];
                }
                
                // JSON parse hatası
                $jsonError = json_last_error_msg();
                $jsonErrorCode = json_last_error();
                Log::error('Iyzico API JSON parse error', [
                    'error' => $jsonError,
                    'error_code' => $jsonErrorCode,
                    'body_preview' => substr($body, 0, 500),
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Iyzico API yanıtı parse edilemedi: ' . $jsonError . ' (Kod: ' . $jsonErrorCode . '). Yanıt: ' . substr($body, 0, 200),
                    'data' => ['raw_response' => substr($body, 0, 500)],
                ];
            }

            // Boş yanıt kontrolü
            if (empty($result) && empty($body)) {
                Log::error('Iyzico API returned empty response');
                return [
                    'success' => false,
                    'error' => 'Iyzico API\'den boş yanıt alındı. API anahtarlarınızı ve base URL\'i kontrol edin. (Test: https://sandbox-api.iyzipay.com)',
                    'data' => [],
                ];
            }
            
            // Eğer result array değilse
            if (!is_array($result)) {
                Log::error('Iyzico API returned non-array response', ['result_type' => gettype($result), 'result' => $result]);
                return [
                    'success' => false,
                    'error' => 'Iyzico API beklenmeyen format döndü. Yanıt tipi: ' . gettype($result),
                    'data' => ['raw_response' => $body],
                ];
            }

            // Iyzico API yanıtını kontrol et
            $status = $result['status'] ?? null;
            
            Log::info('Iyzico API Response Status', [
                'status' => $status,
                'has_subMerchantKey' => isset($result['subMerchantKey']),
                'has_errorMessage' => isset($result['errorMessage']),
                'has_errorCode' => isset($result['errorCode']),
                'full_response' => $result,
            ]);
            
            if ($status === 'success') {
                $subMerchantKey = $result['subMerchantKey'] ?? null;
                
                if (empty($subMerchantKey)) {
                    Log::error('Iyzico API returned success but no subMerchantKey', ['response' => $result]);
                    return [
                        'success' => false,
                        'error' => 'Iyzico API başarılı yanıt döndü ancak subMerchantKey bulunamadı. Lütfen Iyzico destek ekibi ile iletişime geçin.',
                        'data' => $result,
                    ];
                }
                
                return [
                    'success' => true,
                    'sub_merchant_key' => $subMerchantKey,
                    'data' => $result,
                ];
            } else {
                // Iyzico hata mesajlarını parse et
                $errorMessage = $result['errorMessage'] ?? 'Bilinmeyen hata';
                $errorCode = $result['errorCode'] ?? null;
                $errorGroup = $result['errorGroup'] ?? null;
                
                // Iyzico hata kodlarına göre açıklayıcı mesajlar
                $errorDescriptions = [
                    '1' => 'Iyzico onboarding API\'si şu anda aktif değil veya API anahtarlarınız bu özelliği desteklemiyor. Lütfen Iyzico destek ekibi ile iletişime geçin ve onboarding API\'sinin aktif edilmesini talep edin. Onboarding aktif edildikten sonra tekrar deneyebilirsiniz.',
                    '1008' => 'Yetkilendirme hatası. Authorization header formatı hatalı. Lütfen Superadmin panelinden Ödeme Ayarları\'nı kontrol edin.',
                    '1009' => 'API anahtarları geçersiz veya eksik. Lütfen Superadmin panelinden Ödeme Ayarları\'nı kontrol edin.',
                    '1010' => 'Request body formatı hatalı. Lütfen tüm zorunlu alanların doldurulduğundan emin olun.',
                ];
                
                $fullError = $errorMessage;
                if ($errorCode) {
                    $fullError .= ' (Kod: ' . $errorCode . ')';
                    if (isset($errorDescriptions[$errorCode])) {
                        $fullError .= ' - ' . $errorDescriptions[$errorCode];
                    }
                }
                if ($errorGroup) {
                    $fullError .= ' [Grup: ' . $errorGroup . ']';
                }
                
                Log::error('Iyzico Sub-Merchant Creation Failed', [
                    'school_id' => $school->id ?? null,
                    'bank_account_id' => $bankAccount->id ?? null,
                    'error_message' => $errorMessage,
                    'error_code' => $errorCode,
                    'error_group' => $errorGroup,
                    'full_response' => $result,
                    'request_preview' => substr($jsonRequest, 0, 500),
                ]);

                return [
                    'success' => false,
                    'error' => $fullError,
                    'error_code' => $errorCode,
                    'error_group' => $errorGroup,
                    'data' => $result,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Iyzico Create SubMerchant Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'error' => 'Sub-merchant oluşturulurken bir hata oluştu: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Sub-merchant güncelle
     * Iyzico: PUT /onboarding/submerchant, IYZWSv2 imzası (create ile aynı format).
     * İletişim adı/soyadı, adres, e-posta, telefon banka hesabından alınır.
     */
    public function updateSubMerchant($subMerchantKey, $school, $bankAccount)
    {
        try {
            $phoneRaw = $bankAccount->gsm_number ?? $school->phone ?? '5550000000';
            $phone = preg_replace('/[^0-9]/', '', $phoneRaw);
            if (strlen($phone) >= 10 && substr($phone, 0, 2) !== '90') {
                $phone = '90' . ltrim($phone, '0');
            }
            $gsmNumber = (strlen($phone) >= 10) ? '+' . $phone : ('+905550000000');

            // Iyzico güncelleme API'si tüm alt işyeri tiplerinde contactName ve contactSurname zorunlu istiyor.
            // Bireysel'de contact_name/contact_surname kullan; Şahıs/Limited'de eski Bireysel verisi kalmaması için
            // her zaman legal_company_title / account_holder_name'den türet (tip değişince Iyzico'da doğru isim görünsün).
            $contactName = '';
            $contactSurname = '';
            if ($bankAccount->sub_merchant_type === 'PERSONAL') {
                $contactName = trim($bankAccount->contact_name ?? '');
                $contactSurname = trim($bankAccount->contact_surname ?? '');
            }
            if ($contactName === '' || $contactSurname === '') {
                $holder = trim($bankAccount->account_holder_name ?? '');
                $companyTitle = trim($bankAccount->legal_company_title ?? '');
                if ($bankAccount->sub_merchant_type === 'PRIVATE_COMPANY' || $bankAccount->sub_merchant_type === 'LIMITED_OR_JOINT_STOCK_COMPANY') {
                    $contactName = $companyTitle ?: $holder ?: $school->name ?? 'Yetkili';
                    $contactSurname = 'Şirket';
                } elseif (str_contains($holder, ' ')) {
                    $parts = explode(' ', $holder, 2);
                    $contactName = $contactName !== '' ? $contactName : trim($parts[0]);
                    $contactSurname = $contactSurname !== '' ? $contactSurname : trim($parts[1]);
                } else {
                    $contactName = $contactName !== '' ? $contactName : ($holder ?: $school->name ?? 'Yetkili');
                    $contactSurname = $contactSurname !== '' ? $contactSurname : 'Şirket';
                }
                if ($contactSurname === '') {
                    $contactSurname = 'Şirket';
                }
            }

            $request = [
                'locale' => 'tr',
                'conversationId' => 'updatesubmerchant' . $school->id . time(),
                'subMerchantKey' => $subMerchantKey,
                'name' => $school->name,
                'email' => $bankAccount->email ?? $school->email,
                'gsmNumber' => $gsmNumber,
                'address' => $bankAccount->address ?? $school->address ?? 'Istanbul',
                'iban' => $bankAccount->iban ?? '',
                'contactName' => $contactName,
                'contactSurname' => $contactSurname,
                'currency' => 'TRY',
            ];

            if ($bankAccount->sub_merchant_type === 'PERSONAL') {
                $request['identityNumber'] = $bankAccount->identity_number ?? '';
            } elseif ($bankAccount->sub_merchant_type === 'PRIVATE_COMPANY') {
                $request['taxOffice'] = $bankAccount->tax_office ?? '';
                $request['legalCompanyTitle'] = $bankAccount->legal_company_title ?? '';
                if (!empty($bankAccount->tax_number)) {
                    $request['taxNumber'] = $bankAccount->tax_number;
                }
            } elseif ($bankAccount->sub_merchant_type === 'LIMITED_OR_JOINT_STOCK_COMPANY') {
                $request['taxOffice'] = $bankAccount->tax_office ?? '';
                $request['taxNumber'] = $bankAccount->tax_number ?? '';
                $request['legalCompanyTitle'] = $bankAccount->legal_company_title ?? '';
            }

            $uriPath = '/onboarding/submerchant';
            $endpoint = $this->baseUrl . $uriPath;
            $jsonRequest = json_encode($request, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $jsonRequest = trim($jsonRequest);

            $randomKey = (string) (time() * 1000) . bin2hex(random_bytes(4));
            $payload = $randomKey . $uriPath . $jsonRequest;
            $signatureHex = hash_hmac('sha256', $payload, $this->secretKey, false);
            $authString = 'apiKey:' . $this->apiKey . '&randomKey:' . $randomKey . '&signature:' . $signatureHex;
            $authorizationHeader = 'IYZWSv2 ' . base64_encode($authString);

            $client = new \GuzzleHttp\Client(['timeout' => 30, 'verify' => true]);

            $response = $client->put($endpoint, [
                'headers' => [
                    'Authorization' => $authorizationHeader,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'x-iyzi-rnd' => $randomKey,
                ],
                'body' => $jsonRequest,
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $result = json_decode($body, true);

            Log::info('Iyzico Update Sub-Merchant Response', [
                'status_code' => $statusCode,
                'response' => $result,
            ]);

            if ($result && ($result['status'] ?? '') === 'success') {
                return [
                    'success' => true,
                    'data' => $result,
                ];
            }

            return [
                'success' => false,
                'error' => $result['errorMessage'] ?? 'Sub-merchant güncellenemedi',
                'error_code' => $result['errorCode'] ?? null,
                'data' => $result,
            ];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse();
            $body = $response ? $response->getBody()->getContents() : '';
            $result = $body ? json_decode($body, true) : null;
            Log::error('Iyzico Update SubMerchant RequestException', [
                'message' => $e->getMessage(),
                'body' => $body,
            ]);
            return [
                'success' => false,
                'error' => $result['errorMessage'] ?? $e->getMessage(),
                'error_code' => $result['errorCode'] ?? null,
                'data' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Iyzico Update SubMerchant Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Sub-merchant güncellenirken bir hata oluştu: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Ödeme durumu sorgula
     */
    public function checkPaymentStatus($paymentId)
    {
        try {
            $request = [
                'locale' => 'tr',
                'conversationId' => 'check_' . time(),
                'paymentId' => $paymentId,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':' . $this->secretKey),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/payment/retrieve', $request);

            $result = $response->json();

            if ($result && $result['status'] === 'success') {
                return [
                    'success' => true,
                    'status' => $result['paymentStatus'],
                    'data' => $result,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $result['errorMessage'] ?? 'Ödeme durumu sorgulanamadı',
                    'data' => $result,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Iyzico Check Payment Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Ödeme durumu sorgulanırken bir hata oluştu',
            ];
        }
    }

    /**
     * Iyzico API bağlantısını test et
     * Bu metod API key, secret key, base URL ve signature oluşturma mekanizmasını test eder
     */
    public function testConnection()
    {
        $results = [
            'api_key_configured' => false,
            'secret_key_configured' => false,
            'base_url_configured' => false,
            'base_url' => null,
            'is_sandbox' => false,
            'signature_test' => false,
            'api_connection_test' => false,
            'errors' => [],
            'warnings' => [],
            'success' => false,
        ];

        try {
            // 1. API Key kontrolü
            if (empty($this->apiKey)) {
                $results['errors'][] = 'API Key yapılandırılmamış. Lütfen Superadmin panelinden Ödeme Ayarları\'nı kontrol edin.';
            } else {
                $results['api_key_configured'] = true;
                Log::info('Iyzico Test - API Key configured', [
                    'api_key_preview' => substr($this->apiKey, 0, 15) . '...',
                    'api_key_length' => strlen($this->apiKey),
                ]);
            }

            // 2. Secret Key kontrolü
            if (empty($this->secretKey)) {
                $results['errors'][] = 'Secret Key yapılandırılmamış. Lütfen Superadmin panelinden Ödeme Ayarları\'nı kontrol edin.';
            } else {
                $results['secret_key_configured'] = true;
                Log::info('Iyzico Test - Secret Key configured', [
                    'secret_key_length' => strlen($this->secretKey),
                ]);
            }

            // 3. Base URL kontrolü
            if (empty($this->baseUrl)) {
                $results['errors'][] = 'Base URL yapılandırılmamış.';
            } else {
                $results['base_url_configured'] = true;
                $results['base_url'] = $this->baseUrl;
                $results['is_sandbox'] = strpos($this->baseUrl, 'sandbox') !== false;
                
                Log::info('Iyzico Test - Base URL configured', [
                    'base_url' => $this->baseUrl,
                    'is_sandbox' => $results['is_sandbox'],
                ]);

                // Base URL format kontrolü
                if ($results['is_sandbox']) {
                    if ($this->baseUrl !== 'https://sandbox-api.iyzipay.com') {
                        $results['warnings'][] = 'Sandbox Base URL beklenen formatta değil. Beklenen: https://sandbox-api.iyzipay.com, Mevcut: ' . $this->baseUrl;
                    }
                } else {
                    if ($this->baseUrl !== 'https://api.iyzipay.com') {
                        $results['warnings'][] = 'Canlı Base URL beklenen formatta değil. Beklenen: https://api.iyzipay.com, Mevcut: ' . $this->baseUrl;
                    }
                }
            }

            // 4. Signature oluşturma testi
            if ($results['api_key_configured'] && $results['secret_key_configured']) {
                try {
                    $testBody = '{"test":"connection"}';
                    $signatureHash = hash_hmac('sha256', $testBody, $this->secretKey, true);
                    $signature = base64_encode($signatureHash);
                    $authData = $this->apiKey . ':' . $signature;
                    $authorizationHeader = 'IYZWSv2 ' . base64_encode($authData);
                    
                    $results['signature_test'] = true;
                    Log::info('Iyzico Test - Signature generation successful', [
                        'signature_preview' => substr($signature, 0, 20) . '...',
                        'authorization_header_preview' => substr($authorizationHeader, 0, 60) . '...',
                    ]);
                } catch (\Exception $e) {
                    $results['errors'][] = 'Signature oluşturma hatası: ' . $e->getMessage();
                    Log::error('Iyzico Test - Signature generation failed', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // 5. API bağlantı testi (basit bir test request)
            if ($results['api_key_configured'] && $results['secret_key_configured'] && $results['base_url_configured']) {
                try {
                    // Minimal bir test request body oluştur
                    $testRequest = [
                        'locale' => 'tr',
                        'conversationId' => 'test_connection_' . time(),
                    ];
                    
                    $jsonRequest = json_encode($testRequest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    $jsonRequest = trim($jsonRequest);
                    
                    // HMACSHA256 signature oluştur
                    $signatureHash = hash_hmac('sha256', $jsonRequest, $this->secretKey, true);
                    $signature = base64_encode($signatureHash);
                    $authData = $this->apiKey . ':' . $signature;
                    $authorizationHeader = 'IYZWSv2 ' . base64_encode($authData);
                    
                    // Test endpoint'e istek gönder (payment/retrieve endpoint'ini kullanarak)
                    // Bu endpoint minimal bir request kabul eder
                    $testEndpoint = $this->baseUrl . '/payment/retrieve';
                    
                    $client = new Client([
                        'timeout' => 10,
                        'verify' => true,
                    ]);
                    
                    $response = $client->post($testEndpoint, [
                        'headers' => [
                            'Authorization' => $authorizationHeader,
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                        ],
                        'body' => $jsonRequest,
                    ]);
                    
                    $statusCode = $response->getStatusCode();
                    $body = $response->getBody()->getContents();
                    $result = json_decode($body, true);
                    
                    Log::info('Iyzico Test - API Connection Test', [
                        'status_code' => $statusCode,
                        'response_preview' => substr($body, 0, 200),
                        'is_json' => $result !== null,
                    ]);
                    
                    // HTTP 200 alırsak bağlantı başarılı (hata mesajı olsa bile API'ye ulaştık demektir)
                    if ($statusCode === 200) {
                        $results['api_connection_test'] = true;
                        if ($result && isset($result['status'])) {
                            if ($result['status'] === 'success') {
                                $results['success'] = true;
                            } else {
                                // API'ye ulaştık ama işlem başarısız (bu normal, çünkü test verisi gönderiyoruz)
                                $results['warnings'][] = 'API bağlantısı başarılı ancak test işlemi başarısız: ' . ($result['errorMessage'] ?? 'Bilinmeyen hata');
                            }
                        } else {
                            // JSON parse edilemedi ama HTTP 200 aldık
                            $results['warnings'][] = 'API bağlantısı başarılı ancak yanıt beklenen formatta değil.';
                        }
                    } else {
                        $results['errors'][] = 'API bağlantı testi başarısız. HTTP Status: ' . $statusCode;
                    }
                } catch (RequestException $e) {
                    $response = $e->getResponse();
                    if ($response) {
                        $statusCode = $response->getStatusCode();
                        $body = $response->getBody()->getContents();
                        $result = json_decode($body, true);
                        
                        Log::error('Iyzico Test - API Connection Test Failed (with Response)', [
                            'status_code' => $statusCode,
                            'response_preview' => substr($body, 0, 200),
                        ]);
                        
                        // HTTP 200 alırsak bağlantı başarılı
                        if ($statusCode === 200) {
                            $results['api_connection_test'] = true;
                            if ($result && isset($result['status'])) {
                                if ($result['status'] === 'success') {
                                    $results['success'] = true;
                                } else {
                                    $results['warnings'][] = 'API bağlantısı başarılı ancak test işlemi başarısız: ' . ($result['errorMessage'] ?? 'Bilinmeyen hata') . ' (Kod: ' . ($result['errorCode'] ?? 'N/A') . ')';
                                }
                            }
                        } else {
                            $results['errors'][] = 'API bağlantı testi başarısız. HTTP Status: ' . $statusCode . ', Yanıt: ' . substr($body, 0, 200);
                        }
                    } else {
                        $results['errors'][] = 'API\'ye bağlanılamadı: ' . $e->getMessage();
                        Log::error('Iyzico Test - API Connection Test Failed (No Response)', [
                            'error' => $e->getMessage(),
                        ]);
                    }
                } catch (\Exception $e) {
                    $results['errors'][] = 'API bağlantı testi sırasında hata: ' . $e->getMessage();
                    Log::error('Iyzico Test - API Connection Test Exception', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }

            // Genel başarı durumu
            if (empty($results['errors']) && $results['api_key_configured'] && $results['secret_key_configured'] && $results['base_url_configured'] && $results['signature_test'] && $results['api_connection_test']) {
                $results['success'] = true;
            }

        } catch (\Exception $e) {
            $results['errors'][] = 'Test sırasında beklenmeyen hata: ' . $e->getMessage();
            Log::error('Iyzico Test - Unexpected Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return $results;
    }
}
