@extends('layouts.panel')

@section('title', 'Ödeme Ayarları')
@section('page-title', 'Ödeme Ayarları')
@section('page-description', 'Iyzico ödeme entegrasyonu ayarları')

@section('sidebar-menu')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="w-full max-w-none">
    <!-- Test Sonuçları -->
    <div id="test-results" class="hidden mb-4"></div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-900">Ödeme Ayarları</h2>
            <button type="button" id="test-connection-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-plug mr-2"></i>API Bağlantısını Test Et
            </button>
        </div>
        
        <form action="{{ route('superadmin.payment-settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Iyzico API Ayarları -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Iyzico API Ayarları</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                            <input type="text" name="iyzico_api_key" value="{{ old('iyzico_api_key', $settings['iyzico_api_key']) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Iyzico API Key">
                            <p class="text-xs text-gray-500 mt-1">Iyzico panelinden alınan API anahtarı</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Secret Key</label>
                            <input type="password" name="iyzico_secret_key" value="{{ old('iyzico_secret_key', $settings['iyzico_secret_key']) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Iyzico Secret Key">
                            <p class="text-xs text-gray-500 mt-1">Iyzico panelinden alınan gizli anahtar</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Base URL</label>
                            <select name="iyzico_base_url" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="https://api.iyzipay.com" {{ $settings['iyzico_base_url'] == 'https://api.iyzipay.com' ? 'selected' : '' }}>Production (https://api.iyzipay.com)</option>
                                <option value="https://sandbox-api.iyzipay.com" {{ $settings['iyzico_base_url'] == 'https://sandbox-api.iyzipay.com' ? 'selected' : '' }}>Sandbox (https://sandbox-api.iyzipay.com)</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Test için Sandbox, canlı için Production seçin</p>
                        </div>
                    </div>
                </div>

                <!-- Komisyon Ayarları -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Komisyon Ayarları</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Varsayılan Komisyon Oranı (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="default_commission_rate" 
                               value="{{ old('default_commission_rate', $settings['default_commission_rate']) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                        <p class="text-xs text-gray-500 mt-1">Yeni okullar için varsayılan komisyon oranı</p>
                    </div>
                </div>

                <!-- Bilgilendirme -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Önemli:</strong> API anahtarlarınızı güvende tutun. Bu bilgiler .env dosyasında saklanmalıdır.
                                Ayarları değiştirdikten sonra .env dosyası otomatik olarak güncellenir.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Iyzico Onboarding Bilgilendirmesi -->
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Iyzico Onboarding API:</strong> Spor okullarının Iyzico alt işyeri (sub-merchant) oluşturabilmesi için Iyzico onboarding API'sinin aktif olması gerekmektedir. 
                                Eğer onboarding API aktif değilse, Iyzico destek ekibi ile iletişime geçip onboarding API'sinin aktif edilmesini talep edin.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-save mr-2"></i>Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const testBtn = document.getElementById('test-connection-btn');
    const testResults = document.getElementById('test-results');
    
    testBtn.addEventListener('click', function() {
        // Butonu devre dışı bırak
        testBtn.disabled = true;
        testBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Test ediliyor...';
        
        // Test sonuçlarını temizle
        testResults.classList.add('hidden');
        testResults.innerHTML = '';
        
        // AJAX isteği gönder
        fetch('{{ route("superadmin.payment-settings.test-connection") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Butonu tekrar aktif et
            testBtn.disabled = false;
            testBtn.innerHTML = '<i class="fas fa-plug mr-2"></i>API Bağlantısını Test Et';
            
            // Sonuçları göster
            let html = '<div class="bg-white rounded-lg shadow p-6">';
            html += '<h3 class="text-lg font-semibold text-gray-900 mb-4">Iyzico API Test Sonuçları</h3>';
            
            // Başarı durumu
            if (data.success) {
                html += '<div class="bg-green-50 border-l-4 border-green-500 p-4 rounded mb-4">';
                html += '<div class="flex"><div class="flex-shrink-0"><i class="fas fa-check-circle text-green-500"></i></div>';
                html += '<div class="ml-3"><p class="text-sm text-green-700"><strong>Başarılı!</strong> Iyzico API bağlantısı başarıyla test edildi.</p></div></div></div>';
            } else {
                html += '<div class="bg-red-50 border-l-4 border-red-500 p-4 rounded mb-4">';
                html += '<div class="flex"><div class="flex-shrink-0"><i class="fas fa-times-circle text-red-500"></i></div>';
                html += '<div class="ml-3"><p class="text-sm text-red-700"><strong>Hata!</strong> Iyzico API bağlantısı test edilemedi.</p></div></div></div>';
            }
            
            // Detaylı sonuçlar
            html += '<div class="space-y-3">';
            
            // Yapılandırma kontrolleri
            html += '<div class="border rounded-lg p-4">';
            html += '<h4 class="font-semibold text-gray-900 mb-2">Yapılandırma Kontrolleri</h4>';
            html += '<div class="space-y-2">';
            html += '<div class="flex items-center"><span class="w-48 text-sm text-gray-600">API Key:</span><span class="' + (data.api_key_configured ? 'text-green-600' : 'text-red-600') + '"><i class="fas fa-' + (data.api_key_configured ? 'check' : 'times') + ' mr-1"></i>' + (data.api_key_configured ? 'Yapılandırılmış' : 'Yapılandırılmamış') + '</span></div>';
            html += '<div class="flex items-center"><span class="w-48 text-sm text-gray-600">Secret Key:</span><span class="' + (data.secret_key_configured ? 'text-green-600' : 'text-red-600') + '"><i class="fas fa-' + (data.secret_key_configured ? 'check' : 'times') + ' mr-1"></i>' + (data.secret_key_configured ? 'Yapılandırılmış' : 'Yapılandırılmamış') + '</span></div>';
            html += '<div class="flex items-center"><span class="w-48 text-sm text-gray-600">Base URL:</span><span class="' + (data.base_url_configured ? 'text-green-600' : 'text-red-600') + '"><i class="fas fa-' + (data.base_url_configured ? 'check' : 'times') + ' mr-1"></i>' + (data.base_url_configured ? (data.base_url || 'Yapılandırılmış') : 'Yapılandırılmamış') + '</span></div>';
            if (data.base_url_configured) {
                html += '<div class="flex items-center"><span class="w-48 text-sm text-gray-600">Ortam:</span><span class="text-blue-600"><i class="fas fa-info-circle mr-1"></i>' + (data.is_sandbox ? 'Sandbox (Test)' : 'Production (Canlı)') + '</span></div>';
            }
            html += '<div class="flex items-center"><span class="w-48 text-sm text-gray-600">Signature Test:</span><span class="' + (data.signature_test ? 'text-green-600' : 'text-red-600') + '"><i class="fas fa-' + (data.signature_test ? 'check' : 'times') + ' mr-1"></i>' + (data.signature_test ? 'Başarılı' : 'Başarısız') + '</span></div>';
            html += '<div class="flex items-center"><span class="w-48 text-sm text-gray-600">API Bağlantı Testi:</span><span class="' + (data.api_connection_test ? 'text-green-600' : 'text-red-600') + '"><i class="fas fa-' + (data.api_connection_test ? 'check' : 'times') + ' mr-1"></i>' + (data.api_connection_test ? 'Başarılı' : 'Başarısız') + '</span></div>';
            html += '</div></div>';
            
            // Hatalar
            if (data.errors && data.errors.length > 0) {
                html += '<div class="border border-red-300 rounded-lg p-4 bg-red-50">';
                html += '<h4 class="font-semibold text-red-900 mb-2">Hatalar</h4>';
                html += '<ul class="list-disc list-inside space-y-1">';
                data.errors.forEach(function(error) {
                    html += '<li class="text-sm text-red-700">' + error + '</li>';
                });
                html += '</ul></div>';
            }
            
            // Uyarılar
            if (data.warnings && data.warnings.length > 0) {
                html += '<div class="border border-yellow-300 rounded-lg p-4 bg-yellow-50">';
                html += '<h4 class="font-semibold text-yellow-900 mb-2">Uyarılar</h4>';
                html += '<ul class="list-disc list-inside space-y-1">';
                data.warnings.forEach(function(warning) {
                    html += '<li class="text-sm text-yellow-700">' + warning + '</li>';
                });
                html += '</ul></div>';
            }
            
            html += '</div></div>';
            testResults.innerHTML = html;
            testResults.classList.remove('hidden');
        })
        .catch(error => {
            // Butonu tekrar aktif et
            testBtn.disabled = false;
            testBtn.innerHTML = '<i class="fas fa-plug mr-2"></i>API Bağlantısını Test Et';
            
            // Hata mesajı göster
            testResults.innerHTML = '<div class="bg-red-50 border-l-4 border-red-500 p-4 rounded"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-times-circle text-red-500"></i></div><div class="ml-3"><p class="text-sm text-red-700"><strong>Hata!</strong> Test sırasında bir hata oluştu: ' + error.message + '</p></div></div></div>';
            testResults.classList.remove('hidden');
        });
    });
});
</script>
@endpush
@endsection
