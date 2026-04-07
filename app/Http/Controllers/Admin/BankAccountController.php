<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Services\IyzicoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BankAccountController extends Controller
{
    protected $iyzicoService;

    public function __construct(IyzicoService $iyzicoService)
    {
        $this->iyzicoService = $iyzicoService;
    }
    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $bankAccounts = BankAccount::where('school_id', $schoolId)->paginate(15);
        return view('admin.bank-accounts.index', compact('bankAccounts'));
    }

    public function create()
    {
        $schoolId = Auth::user()->school_id;
        if (BankAccount::where('school_id', $schoolId)->exists()) {
            return redirect()->route('admin.bank-accounts.index')
                ->with('info', 'Zaten bir banka hesabınız bulunmaktadır. Değişiklik yapmak için mevcut hesabı düzenleyebilirsiniz.');
        }
        return view('admin.bank-accounts.create');
    }

    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        // Okul başına yalnızca bir banka hesabına izin ver
        if (BankAccount::where('school_id', $schoolId)->exists()) {
            return redirect()->route('admin.bank-accounts.index')
                ->with('info', 'Zaten bir banka hesabınız bulunmaktadır. Değişiklik yapmak için mevcut hesabı düzenleyebilirsiniz.');
        }
        
        // Tek aktif IBAN kontrolü - Iyzico için sadece bir aktif IBAN olmalı
        if ($request->has('is_active') && $request->is_active) {
            $activeAccount = BankAccount::where('school_id', $schoolId)
                ->where('is_active', true)
                ->where('id', '!=', $request->input('id')) // Update için
                ->first();
            
            if ($activeAccount) {
                return back()
                    ->withInput()
                    ->with('error', 'Zaten aktif bir banka hesabınız var. Iyzico ödemeleri için sadece bir aktif hesap kullanılabilir. Lütfen önce mevcut aktif hesabı pasif yapın.');
            }
        }

        // Sub-merchant tipine göre validation
        $rules = [
            'account_holder_name' => 'required|string|max:255',
            'iban' => 'required|string|max:34|regex:/^TR[0-9]{24}$/',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'sub_merchant_type' => 'required|in:PERSONAL,PRIVATE_COMPANY,LIMITED_OR_JOINT_STOCK_COMPANY',
            'gsm_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|min:10|max:500',
        ];

        // PERSONAL (Bireysel) için zorunlu alanlar
        if ($request->sub_merchant_type === 'PERSONAL') {
            $rules['identity_number'] = 'required|string|size:11';
            $rules['contact_name'] = 'required|string|max:255';
            $rules['contact_surname'] = 'required|string|max:255';
        }

        // PRIVATE_COMPANY (Şahıs Şirketi) için zorunlu alanlar
        if ($request->sub_merchant_type === 'PRIVATE_COMPANY') {
            $rules['tax_office'] = 'required|string|max:255';
            $rules['legal_company_title'] = 'required|string|max:255';
            $rules['tax_number'] = 'required|string|max:20';
        }

        // LIMITED_OR_JOINT_STOCK_COMPANY (Limited/Anonim Şirket) için zorunlu alanlar
        if ($request->sub_merchant_type === 'LIMITED_OR_JOINT_STOCK_COMPANY') {
            $rules['tax_office'] = 'required|string|max:255';
            $rules['tax_number'] = 'required|string|max:20';
            $rules['legal_company_title'] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        $validated['school_id'] = $schoolId;
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;
        $validated['is_verified'] = false;

        $bankAccount = BankAccount::create($validated);

        $school = Auth::user()->school;

        // Okulda zaten Iyzico alt işyeri key varsa create yerine update kullan (2002 "zaten var" hatasını önler)
        if (!empty($school->iyzico_sub_merchant_key)) {
            $subMerchantResult = $this->iyzicoService->updateSubMerchant($school->iyzico_sub_merchant_key, $school, $bankAccount);
            if ($subMerchantResult['success']) {
                $bankAccount->update(['is_verified' => true]);
                return redirect()->route('admin.bank-accounts.index')
                    ->with('success', 'Banka hesabı eklendi ve Iyzico üye alt işyeri bilgileri güncellendi.');
            }
            $errorMessage = $subMerchantResult['error'] ?? 'Bilinmeyen hata';
            return redirect()->route('admin.bank-accounts.index')
                ->with('warning', 'Banka hesabı eklendi ancak Iyzico üzerinde güncelleme yapılamadı: ' . $errorMessage);
        }

        // Yeni alt işyeri oluştur
        $subMerchantResult = $this->iyzicoService->createSubMerchant($school, $bankAccount);

        if ($subMerchantResult['success']) {
            $school->update([
                'iyzico_sub_merchant_key' => $subMerchantResult['sub_merchant_key'],
            ]);
            $bankAccount->update(['is_verified' => true]);

            return redirect()->route('admin.bank-accounts.index')
                ->with('success', 'Banka hesabı eklendi ve Iyzico üye alt işyeri (sub-merchant) otomatik oluşturuldu.');
        }

        // 2002 = Iyzico'da bu okul için alt işyeri zaten var (örn. önceki kayıt). Key'i bilmiyorsak hesabı yine doğrulanmış sayıp kullanıcıya bilgi ver.
        $errorCode = $subMerchantResult['error_code'] ?? null;
        if ($errorCode === '2002') {
            return redirect()->route('admin.bank-accounts.index')
                ->with('warning', 'Banka hesabı eklendi. Iyzico\'da bu okul için zaten bir alt işyeri kaydı bulunuyor. Hesabı düzenleyerek bilgileri güncelleyebilirsiniz.');
        }

        $errorMessage = $subMerchantResult['error'] ?? 'Bilinmeyen hata';
        if ($errorCode === '1') {
            $errorMessage = 'Iyzico onboarding API\'si şu anda aktif değil. Lütfen Iyzico destek ekibi ile iletişime geçin. ';
            $errorMessage .= 'Onboarding aktif edildikten sonra listeden "Sub-Merchant Oluştur" butonunu kullanarak tekrar deneyebilirsiniz.';
        }

        return redirect()->route('admin.bank-accounts.index')
            ->with('warning', 'Banka hesabı eklendi ancak Iyzico üye alt işyeri oluşturulamadı: ' . $errorMessage);
    }

    public function edit(BankAccount $bankAccount)
    {
        if ($bankAccount->school_id != Auth::user()->school_id) {
            abort(403);
        }
        return view('admin.bank-accounts.edit', compact('bankAccount'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        if ($bankAccount->school_id != Auth::user()->school_id) {
            abort(403);
        }

        $schoolId = Auth::user()->school_id;

        // Bu hesabı aktif yapıyorsak, diğer hesapları pasif yap (tek aktif hesap kuralı)
        if ($request->has('is_active') && $request->is_active) {
            BankAccount::where('school_id', $schoolId)
                ->where('id', '!=', $bankAccount->id)
                ->update(['is_active' => false]);
        }

        // Sub-merchant tipine göre validation
        $rules = [
            'account_holder_name' => 'required|string|max:255',
            'iban' => 'required|string|max:34|regex:/^TR[0-9]{24}$/',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'sub_merchant_type' => 'required|in:PERSONAL,PRIVATE_COMPANY,LIMITED_OR_JOINT_STOCK_COMPANY',
            'is_active' => 'boolean',
            'gsm_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|min:10|max:500',
        ];

        // PERSONAL (Bireysel) için zorunlu alanlar
        if ($request->sub_merchant_type === 'PERSONAL') {
            $rules['identity_number'] = 'required|string|size:11';
            $rules['contact_name'] = 'required|string|max:255';
            $rules['contact_surname'] = 'required|string|max:255';
        }

        // PRIVATE_COMPANY (Şahıs Şirketi) için zorunlu alanlar
        if ($request->sub_merchant_type === 'PRIVATE_COMPANY') {
            $rules['tax_office'] = 'required|string|max:255';
            $rules['legal_company_title'] = 'required|string|max:255';
            $rules['tax_number'] = 'required|string|max:20';
        }

        // LIMITED_OR_JOINT_STOCK_COMPANY (Limited/Anonim Şirket) için zorunlu alanlar
        if ($request->sub_merchant_type === 'LIMITED_OR_JOINT_STOCK_COMPANY') {
            $rules['tax_office'] = 'required|string|max:255';
            $rules['tax_number'] = 'required|string|max:20';
            $rules['legal_company_title'] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        // Tip değişince diğer tipin alanlarını temizle; Iyzico'ya yanlış isim gitmesin
        if ($request->sub_merchant_type === 'PERSONAL') {
            $validated['tax_office'] = null;
            $validated['legal_company_title'] = null;
            $validated['tax_number'] = null;
        } elseif ($request->sub_merchant_type === 'PRIVATE_COMPANY' || $request->sub_merchant_type === 'LIMITED_OR_JOINT_STOCK_COMPANY') {
            $validated['contact_name'] = null;
            $validated['contact_surname'] = null;
            $validated['identity_number'] = null;
        }

        $bankAccount->update($validated);

        // Sub-merchant key varsa ve bu hesap aktifse Iyzico'da da güncelle (iletişim adı, adres vb.)
        $school = Auth::user()->school;
        if ($school->iyzico_sub_merchant_key && $bankAccount->is_active) {
            $updateResult = $this->iyzicoService->updateSubMerchant(
                $school->iyzico_sub_merchant_key,
                $school,
                $bankAccount
            );
            if (!$updateResult['success']) {
                return redirect()->route('admin.bank-accounts.index')
                    ->with('warning', 'Banka hesabı güncellendi ancak Iyzico üzerinde güncelleme yapılamadı: ' . ($updateResult['error'] ?? 'Bilinmeyen hata'));
            }
        }

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'Banka hesabı ve Iyzico bilgileri başarıyla güncellendi.');
    }

    /**
     * Iyzico Sub-Merchant oluştur
     */
    public function createSubMerchant(BankAccount $bankAccount)
    {
        if ($bankAccount->school_id != Auth::user()->school_id) {
            abort(403);
        }

        // Sub-merchant tipi kontrolü
        if (!$bankAccount->sub_merchant_type) {
            return redirect()->route('admin.bank-accounts.index')
                ->with('error', 'Sub-merchant oluşturmak için önce alt işyeri tipini seçmelisiniz. Lütfen hesabı düzenleyin ve alt işyeri tipini seçin.');
        }

        $school = Auth::user()->school;
        
        $subMerchantResult = $this->iyzicoService->createSubMerchant($school, $bankAccount);
        
        if ($subMerchantResult['success']) {
            $school->update([
                'iyzico_sub_merchant_key' => $subMerchantResult['sub_merchant_key']
            ]);
            $bankAccount->update(['is_verified' => true]);
            
            return redirect()->route('admin.bank-accounts.index')
                ->with('success', 'Iyzico alt işyeri (sub-merchant) başarıyla oluşturuldu.');
        } else {
            $errorMessage = $subMerchantResult['error'] ?? 'Bilinmeyen hata';
            $errorDetails = '';
            
            // Özel hata mesajları
            if (isset($subMerchantResult['error_code'])) {
                $errorCode = $subMerchantResult['error_code'];
                
                if ($errorCode === '1') {
                    // Onboarding API aktif değil veya desteklenmiyor
                    $errorMessage = 'Iyzico onboarding API\'si şu anda aktif değil veya API anahtarlarınız bu özelliği desteklemiyor. ';
                    $errorMessage .= 'Lütfen Iyzico destek ekibi ile iletişime geçin ve onboarding API\'sinin aktif edilmesini talep edin. ';
                    $errorMessage .= 'Onboarding aktif edildikten sonra tekrar deneyebilirsiniz.';
                } elseif ($errorCode === '1008') {
                    $errorMessage = 'Yetkilendirme hatası. Lütfen Superadmin panelinden Ödeme Ayarları\'nı kontrol edin.';
                } else {
                    $errorDetails .= ' (Hata Kodu: ' . $errorCode . ')';
                }
            }
            
            if (isset($subMerchantResult['data']) && is_array($subMerchantResult['data'])) {
                Log::error('Iyzico Sub-Merchant Creation Failed', [
                    'school_id' => $school->id,
                    'bank_account_id' => $bankAccount->id,
                    'error' => $subMerchantResult,
                ]);
            }
            
            return redirect()->route('admin.bank-accounts.index')
                ->with('error', 'Sub-merchant oluşturulamadı: ' . $errorMessage . $errorDetails);
        }
    }

    public function destroy(BankAccount $bankAccount)
    {
        if ($bankAccount->school_id != Auth::user()->school_id) {
            abort(403);
        }

        $bankAccount->delete();

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'Banka hesabı başarıyla silindi.');
    }
}
