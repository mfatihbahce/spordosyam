@extends('layouts.panel')

@section('title', 'Banka Hesabı Düzenle')
@section('page-title', 'Banka Hesabı Düzenle')
@section('page-description', 'Banka hesabı bilgilerini güncelleyin')

@section('sidebar-menu')
    @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.bank-accounts.update', $bankAccount) }}" method="POST" id="bankAccountForm">
            @csrf
            @method('PUT')
            
            <!-- Temel Banka Bilgileri -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Banka Hesabı Bilgileri</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hesap Sahibi <span class="text-red-500">*</span></label>
                        <input type="text" name="account_holder_name" value="{{ old('account_holder_name', $bankAccount->account_holder_name) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">IBAN <span class="text-red-500">*</span></label>
                        <input type="text" name="iban" value="{{ old('iban', $bankAccount->iban) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="TR180006200119000006672315" required>
                        <p class="text-xs text-gray-500 mt-1">TR ile başlayan 26 karakterli IBAN numarası</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Banka Adı</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name', $bankAccount->bank_name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Şube Adı</label>
                            <input type="text" name="branch_name" value="{{ old('branch_name', $bankAccount->branch_name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $bankAccount->is_active) ? 'checked' : '' }} 
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Aktif</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">Iyzico ödemeleri için sadece bir aktif hesap kullanılabilir</p>
                    </div>
                </div>
            </div>

            <!-- Iyzico Alt İşyeri Bilgileri -->
            <div class="mb-8 border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Iyzico Alt İşyeri (Sub-Merchant) Bilgileri</h3>
                <p class="text-sm text-gray-600 mb-4">Iyzico'da otomatik ödeme dağıtımı için alt işyeri kaydı oluşturulacaktır.</p>
                
                @if($bankAccount->is_verified || Auth::user()->school->iyzico_sub_merchant_key)
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">
                                    <strong>Iyzico Entegrasyonu Aktif:</strong> Bu banka hesabı Iyzico'da kayıtlıdır ve ödemeler otomatik olarak bu hesaba yatırılacaktır.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <strong>Bilgi:</strong> Yeni banka hesabı eklerken üye alt işyeri otomatik oluşturulur. Bu hesap için henüz oluşturulmadıysa, IBAN listesindeki "Sub-Merchant Oluştur" butonu ile oluşturabilirsiniz.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Iyzico için zorunlu iletişim bilgileri -->
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded mb-4">
                    <p class="text-sm text-yellow-700"><strong>Önemli:</strong> Iyzico alt işyeri oluşturmak için aşağıdaki iletişim bilgileri zorunludur.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefon Numarası (GSM) <span class="text-red-500">*</span></label>
                        <input type="text" name="gsm_number" id="gsm_number" value="{{ old('gsm_number', $bankAccount->gsm_number ?? Auth::user()->school->phone ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="05551234567" required>
                        <p class="text-xs text-gray-500 mt-1">Iyzico için zorunlu. Örn: 05551234567 veya +905551234567</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">E-posta Adresi <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $bankAccount->email ?? Auth::user()->school->email ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="ornek@email.com" required>
                        <p class="text-xs text-gray-500 mt-1">Iyzico için zorunlu</p>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adres <span class="text-red-500">*</span></label>
                    <textarea name="address" id="address" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                              placeholder="Tam adres bilgisi (en az 10 karakter)" required>{{ old('address', $bankAccount->address ?? Auth::user()->school->address ?? '') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Iyzico için zorunlu. En az 10 karakter olmalıdır.</p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alt İşyeri Tipi <span class="text-red-500">*</span></label>
                    <select name="sub_merchant_type" id="sub_merchant_type" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Seçiniz...</option>
                        <option value="PERSONAL" {{ old('sub_merchant_type', $bankAccount->sub_merchant_type) == 'PERSONAL' ? 'selected' : '' }}>Bireysel</option>
                        <option value="PRIVATE_COMPANY" {{ old('sub_merchant_type', $bankAccount->sub_merchant_type) == 'PRIVATE_COMPANY' ? 'selected' : '' }}>Şahıs Şirketi</option>
                        <option value="LIMITED_OR_JOINT_STOCK_COMPANY" {{ old('sub_merchant_type', $bankAccount->sub_merchant_type) == 'LIMITED_OR_JOINT_STOCK_COMPANY' ? 'selected' : '' }}>Limited / Anonim Şirketi</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Iyzico ödemeleri için alt işyeri tipini seçin</p>
                </div>

                <!-- Bireysel (PERSONAL) Alanları -->
                <div id="personal_fields" class="hidden space-y-4">
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <p class="text-sm text-blue-700"><strong>Bireysel</strong> alt işyeri için aşağıdaki bilgiler zorunludur.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">TC Kimlik No <span class="text-red-500">*</span></label>
                            <input type="text" name="identity_number" id="identity_number" value="{{ old('identity_number', $bankAccount->identity_number) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                                   maxlength="11" pattern="[0-9]{11}" placeholder="11111111111">
                            <p class="text-xs text-gray-500 mt-1">11 haneli TC Kimlik numarası</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adı <span class="text-red-500">*</span></label>
                            <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name', $bankAccount->contact_name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Ahmet">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Soyadı <span class="text-red-500">*</span></label>
                        <input type="text" name="contact_surname" id="contact_surname" value="{{ old('contact_surname', $bankAccount->contact_surname) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Yılmaz">
                    </div>
                </div>

                <!-- Şahıs Şirketi (PRIVATE_COMPANY) Alanları -->
                <div id="private_company_fields" class="hidden space-y-4">
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <p class="text-sm text-blue-700"><strong>Şahıs Şirketi</strong> alt işyeri için aşağıdaki bilgiler zorunludur.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vergi Dairesi <span class="text-red-500">*</span></label>
                        <input type="text" name="tax_office" id="tax_office_private" value="{{ old('tax_office', $bankAccount->tax_office) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="Örn: İstanbul Vergi Dairesi">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Yasal Şirket Unvanı <span class="text-red-500">*</span></label>
                        <input type="text" name="legal_company_title" id="legal_company_title_private" value="{{ old('legal_company_title', $bankAccount->legal_company_title) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="Örn: Ahmet Yılmaz Spor Okulu">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vergi Numarası <span class="text-red-500">*</span></label>
                        <input type="text" name="tax_number" id="tax_number_private" value="{{ old('tax_number', $bankAccount->tax_number) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="1234567890" required>
                        <p class="text-xs text-gray-500 mt-1">Şahıs şirketi vergi numarası</p>
                    </div>
                </div>

                <!-- Limited/Anonim Şirketi (LIMITED_OR_JOINT_STOCK_COMPANY) Alanları -->
                <div id="limited_company_fields" class="hidden space-y-4">
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <p class="text-sm text-blue-700"><strong>Limited / Anonim Şirketi</strong> alt işyeri için aşağıdaki bilgiler zorunludur.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vergi Dairesi <span class="text-red-500">*</span></label>
                        <input type="text" name="tax_office" id="tax_office_limited" value="{{ old('tax_office', $bankAccount->tax_office) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="Örn: İstanbul Vergi Dairesi">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vergi Numarası <span class="text-red-500">*</span></label>
                        <input type="text" name="tax_number" id="tax_number_limited" value="{{ old('tax_number', $bankAccount->tax_number) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="1234567890">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Yasal Şirket Unvanı <span class="text-red-500">*</span></label>
                        <input type="text" name="legal_company_title" id="legal_company_title_limited" value="{{ old('legal_company_title', $bankAccount->legal_company_title) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="Örn: ABC Spor Okulu A.Ş.">
                    </div>
                </div>
            </div>

            <!-- Uyarı -->
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Önemli:</strong> Iyzico ödemeleri için sadece bir aktif banka hesabı kullanılabilir. 
                            Bu hesabı aktif yaparsanız, diğer aktif hesap otomatik olarak pasif yapılacaktır.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.bank-accounts.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    İptal
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-save mr-2"></i>Güncelle
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const subMerchantType = document.getElementById('sub_merchant_type');
    const personalFields = document.getElementById('personal_fields');
    const privateCompanyFields = document.getElementById('private_company_fields');
    const limitedCompanyFields = document.getElementById('limited_company_fields');

    function setSectionInputsDisabled(sectionEl, disabled) {
        if (!sectionEl) return;
        sectionEl.querySelectorAll('input, select, textarea').forEach(function(input) {
            input.disabled = disabled;
        });
    }

    function toggleFields() {
        // Tüm alanları gizle ve disabled yap (gönderilmesin)
        personalFields.classList.add('hidden');
        privateCompanyFields.classList.add('hidden');
        limitedCompanyFields.classList.add('hidden');
        setSectionInputsDisabled(personalFields, true);
        setSectionInputsDisabled(privateCompanyFields, true);
        setSectionInputsDisabled(limitedCompanyFields, true);

        // Tüm alanları önce opsiyonel yap
        const identityNumber = document.getElementById('identity_number');
        const contactName = document.getElementById('contact_name');
        const contactSurname = document.getElementById('contact_surname');
        const taxOfficePrivate = document.getElementById('tax_office_private');
        const taxOfficeLimited = document.getElementById('tax_office_limited');
        const taxNumberPrivate = document.getElementById('tax_number_private');
        const taxNumberLimited = document.getElementById('tax_number_limited');
        const legalCompanyTitlePrivate = document.getElementById('legal_company_title_private');
        const legalCompanyTitleLimited = document.getElementById('legal_company_title_limited');

        // Seçilen tipe göre alanları göster, zorunlu yap ve disabled kaldır
        switch(subMerchantType.value) {
            case 'PERSONAL':
                personalFields.classList.remove('hidden');
                setSectionInputsDisabled(personalFields, false);
                if (identityNumber) identityNumber.required = true;
                if (contactName) contactName.required = true;
                if (contactSurname) contactSurname.required = true;
                if (taxOfficePrivate) taxOfficePrivate.required = false;
                if (taxOfficeLimited) taxOfficeLimited.required = false;
                if (taxNumberPrivate) taxNumberPrivate.required = false;
                if (taxNumberLimited) taxNumberLimited.required = false;
                if (legalCompanyTitlePrivate) legalCompanyTitlePrivate.required = false;
                if (legalCompanyTitleLimited) legalCompanyTitleLimited.required = false;
                break;
            case 'PRIVATE_COMPANY':
                privateCompanyFields.classList.remove('hidden');
                setSectionInputsDisabled(privateCompanyFields, false);
                if (taxOfficePrivate) taxOfficePrivate.required = true;
                if (legalCompanyTitlePrivate) legalCompanyTitlePrivate.required = true;
                if (taxNumberPrivate) taxNumberPrivate.required = true;
                if (identityNumber) identityNumber.required = false;
                if (contactName) contactName.required = false;
                if (contactSurname) contactSurname.required = false;
                if (taxOfficeLimited) taxOfficeLimited.required = false;
                if (taxNumberLimited) taxNumberLimited.required = false;
                if (legalCompanyTitleLimited) legalCompanyTitleLimited.required = false;
                break;
            case 'LIMITED_OR_JOINT_STOCK_COMPANY':
                limitedCompanyFields.classList.remove('hidden');
                setSectionInputsDisabled(limitedCompanyFields, false);
                if (taxOfficeLimited) taxOfficeLimited.required = true;
                if (taxNumberLimited) taxNumberLimited.required = true;
                if (legalCompanyTitleLimited) legalCompanyTitleLimited.required = true;
                if (identityNumber) identityNumber.required = false;
                if (contactName) contactName.required = false;
                if (contactSurname) contactSurname.required = false;
                if (taxOfficePrivate) taxOfficePrivate.required = false;
                if (taxNumberPrivate) taxNumberPrivate.required = false;
                if (legalCompanyTitlePrivate) legalCompanyTitlePrivate.required = false;
                break;
            default:
                if (identityNumber) identityNumber.required = false;
                if (contactName) contactName.required = false;
                if (contactSurname) contactSurname.required = false;
                if (taxOfficePrivate) taxOfficePrivate.required = false;
                if (taxOfficeLimited) taxOfficeLimited.required = false;
                if (taxNumberPrivate) taxNumberPrivate.required = false;
                if (taxNumberLimited) taxNumberLimited.required = false;
                if (legalCompanyTitlePrivate) legalCompanyTitlePrivate.required = false;
                if (legalCompanyTitleLimited) legalCompanyTitleLimited.required = false;
        }
    }

    // Form submit edilmeden önce validation
    document.getElementById('bankAccountForm').addEventListener('submit', function(e) {
        const subMerchantTypeValue = subMerchantType.value;
        
        if (!subMerchantTypeValue) {
            e.preventDefault();
            alert('Lütfen alt işyeri tipini seçin.');
            subMerchantType.focus();
            return false;
        }

        // Seçilen tipe göre zorunlu alanları kontrol et
        let isValid = true;
        let errorMessage = '';

        if (subMerchantTypeValue === 'PERSONAL') {
            const identityNumber = document.getElementById('identity_number');
            const contactName = document.getElementById('contact_name');
            const contactSurname = document.getElementById('contact_surname');
            
            if (!identityNumber || !identityNumber.value || identityNumber.value.length !== 11) {
                isValid = false;
                errorMessage = 'TC Kimlik No 11 haneli olmalıdır.';
            } else if (!contactName || !contactName.value.trim()) {
                isValid = false;
                errorMessage = 'Ad alanı zorunludur.';
            } else if (!contactSurname || !contactSurname.value.trim()) {
                isValid = false;
                errorMessage = 'Soyad alanı zorunludur.';
            }
        } else if (subMerchantTypeValue === 'PRIVATE_COMPANY') {
            const taxOffice = document.getElementById('tax_office_private');
            const legalCompanyTitle = document.getElementById('legal_company_title_private');
            const taxNumber = document.getElementById('tax_number_private');
            
            if (!taxOffice || !taxOffice.value.trim()) {
                isValid = false;
                errorMessage = 'Vergi Dairesi alanı zorunludur.';
            } else if (!legalCompanyTitle || !legalCompanyTitle.value.trim()) {
                isValid = false;
                errorMessage = 'Yasal Şirket Unvanı alanı zorunludur.';
            } else if (!taxNumber || !taxNumber.value.trim()) {
                isValid = false;
                errorMessage = 'Vergi Numarası alanı zorunludur.';
            }
        } else if (subMerchantTypeValue === 'LIMITED_OR_JOINT_STOCK_COMPANY') {
            const taxOffice = document.getElementById('tax_office_limited');
            const taxNumber = document.getElementById('tax_number_limited');
            const legalCompanyTitle = document.getElementById('legal_company_title_limited');
            
            if (!taxOffice || !taxOffice.value.trim()) {
                isValid = false;
                errorMessage = 'Vergi Dairesi alanı zorunludur.';
            } else if (!taxNumber || !taxNumber.value.trim()) {
                isValid = false;
                errorMessage = 'Vergi Numarası alanı zorunludur.';
            } else if (!legalCompanyTitle || !legalCompanyTitle.value.trim()) {
                isValid = false;
                errorMessage = 'Yasal Şirket Unvanı alanı zorunludur.';
            }
        }

        // GSM, Email, Address kontrolü (her zaman zorunlu)
        const gsmNumber = document.getElementById('gsm_number');
        const email = document.getElementById('email');
        const address = document.getElementById('address');

        if (!gsmNumber || !gsmNumber.value.trim()) {
            isValid = false;
            errorMessage = 'Telefon Numarası (GSM) alanı zorunludur.';
        } else if (!email || !email.value.trim()) {
            isValid = false;
            errorMessage = 'E-posta Adresi alanı zorunludur.';
        } else if (!address || !address.value.trim() || address.value.trim().length < 10) {
            isValid = false;
            errorMessage = 'Adres alanı zorunludur ve en az 10 karakter olmalıdır.';
        }

        if (!isValid) {
            e.preventDefault();
            alert(errorMessage);
            return false;
        }
    });

    subMerchantType.addEventListener('change', toggleFields);
    toggleFields(); // Sayfa yüklendiğinde de çalıştır
});
</script>
@endpush
@endsection
