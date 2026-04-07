<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NetGsmService
{
    protected string $username;
    protected string $password;
    protected string $msgheader;

    public function __construct(?string $username = null, ?string $password = null, ?string $msgheader = null)
    {
        $this->username = $username ?? (SiteSetting::get('netgsm_username') ?: config('services.netgsm.username', ''));
        $this->password = $password ?? (SiteSetting::get('netgsm_password') ?: config('services.netgsm.password', ''));
        $this->msgheader = $msgheader ?? (SiteSetting::get('netgsm_msgheader') ?: config('services.netgsm.msgheader', ''));
    }

    /**
     * Tek numaraya SMS gönder.
     * $phone: 5xxxxxxxxx veya 905xxxxxxxxx formatında.
     */
    public function send(string $phone, string $message): array
    {
        $phone = $this->normalizePhone($phone);
        if (empty($phone) || strlen($phone) < 10) {
            return ['success' => false, 'error' => 'Geçersiz telefon numarası'];
        }

        if (empty($this->username) || empty($this->password) || empty($this->msgheader)) {
            Log::warning('NetGSM: Kullanıcı adı, şifre veya başlık tanımlı değil.');
            return ['success' => false, 'error' => 'NetGSM ayarları eksik'];
        }

        $url = config('services.netgsm.endpoint', 'https://api.netgsm.com.tr/sms/send/get');

        // NetGSM GET API: /send/get/usr/pwd/msgheader/gsmno/message
        $query = http_build_query([
            'usercode' => $this->username,
            'password' => $this->password,
            'gsmno' => $phone,
            'message' => $message,
            'msgheader' => $this->msgheader,
        ]);

        try {
            $response = Http::timeout(15)
                ->get($url . '?' . $query);

            $body = trim($response->body());

            if ($response->successful()) {
                // Eğer NetGSM hata kodlarından biri ise hata olarak döndür
                $knownErrors = ['20','30','40','50','51','60','70','80','85'];
                if (in_array($body, $knownErrors, true)) {
                    $errorMsg = $this->errorMessage($body);
                    Log::warning('NetGSM SMS hatası', ['code' => $body, 'phone' => $phone]);
                    return ['success' => false, 'error' => $errorMsg, 'code' => $body];
                }

                // Başarılı gönderim: genelde job id veya 00/0 döner
                if (str_starts_with($body, '00') || str_starts_with($body, '0') || is_numeric($body)) {
                    return ['success' => true, 'response' => $body];
                }
            }

            $errorMsg = $this->errorMessage($body);
            Log::warning('NetGSM SMS hatası', ['code' => $body, 'phone' => $phone]);
            return ['success' => false, 'error' => $errorMsg, 'code' => $body];
        } catch (\Exception $e) {
            Log::error('NetGSM SMS exception', ['message' => $e->getMessage(), 'phone' => $phone]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '90') && strlen($phone) === 12) {
            return $phone;
        }
        if (str_starts_with($phone, '0') && strlen($phone) === 11) {
            return '9' . $phone;
        }
        if (strlen($phone) === 10 && str_starts_with($phone, '5')) {
            return '90' . $phone;
        }
        return $phone;
    }

    protected function errorMessage(string $code): string
    {
        return match ($code) {
            '20' => 'Mesaj metni veya karakter sayısı hatası',
            '30' => 'Geçersiz kullanıcı adı, şifre veya API yetkisi yok',
            '40' => 'Mesaj başlığı (gönderici adı) tanımlı değil',
            '50', '51' => 'IYS onayı gerekli',
            '70' => 'Parametre hatası',
            '80' => 'Gönderim limiti aşıldı',
            '85' => 'Aynı numaraya 1 dakikada 20’den fazla istek',
            default => 'SMS gönderilemedi (Kod: ' . $code . ')',
        };
    }

    public function isConfigured(): bool
    {
        return !empty($this->username) && !empty($this->password) && !empty($this->msgheader);
    }
}
