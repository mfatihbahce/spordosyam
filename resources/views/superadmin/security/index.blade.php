@extends('layouts.panel')

@section('title', 'Güvenlik')
@section('page-title', 'Güvenlik Ayarları')
@section('page-description', 'Sistem güvenlik politikalarını yönetin')

@section('sidebar-menu')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="w-full max-w-none">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('superadmin.security.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Şifre Politikaları -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Şifre Politikaları</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Şifre Uzunluğu</label>
                            <input type="number" min="6" max="32" name="password_min_length" 
                                   value="{{ old('password_min_length', $settings['password_min_length']) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                            <p class="text-xs text-gray-500 mt-1">Kullanıcı şifrelerinin minimum karakter sayısı</p>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Şifre Gereksinimleri</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="password_require_uppercase" value="1" 
                                           {{ old('password_require_uppercase', $settings['password_require_uppercase']) ? 'checked' : '' }} 
                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Büyük harf zorunlu (A-Z)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="password_require_lowercase" value="1" 
                                           {{ old('password_require_lowercase', $settings['password_require_lowercase']) ? 'checked' : '' }} 
                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Küçük harf zorunlu (a-z)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="password_require_numbers" value="1" 
                                           {{ old('password_require_numbers', $settings['password_require_numbers']) ? 'checked' : '' }} 
                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Rakam zorunlu (0-9)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="password_require_symbols" value="1" 
                                           {{ old('password_require_symbols', $settings['password_require_symbols']) ? 'checked' : '' }} 
                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Özel karakter zorunlu (!@#$%^&*)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Oturum Ayarları -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Oturum Ayarları</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Oturum Süresi (Dakika)</label>
                            <input type="number" min="1" max="1440" name="session_lifetime" 
                                   value="{{ old('session_lifetime', $settings['session_lifetime']) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                            <p class="text-xs text-gray-500 mt-1">Kullanıcı oturumunun kaç dakika aktif kalacağı</p>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="session_encrypt" value="1" 
                                       {{ old('session_encrypt', $settings['session_encrypt']) ? 'checked' : '' }} 
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Oturum Verilerini Şifrele</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1">Oturum verilerinin şifrelenerek saklanması (önerilir)</p>
                        </div>
                    </div>
                </div>

                <!-- İki Faktörlü Kimlik Doğrulama -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">İki Faktörlü Kimlik Doğrulama</h3>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="two_factor_enabled" value="1" 
                                   {{ old('two_factor_enabled', $settings['two_factor_enabled']) ? 'checked' : '' }} 
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">İki Faktörlü Kimlik Doğrulamayı Etkinleştir</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">Kullanıcıların giriş yaparken ek bir doğrulama kodu girmelerini gerektirir</p>
                    </div>
                </div>

                <!-- Güvenlik Uyarısı -->
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Uyarı:</strong> Güvenlik ayarlarını değiştirdikten sonra .env dosyasını manuel olarak güncellemeyi unutmayın.
                                Değişikliklerin etkili olması için uygulamayı yeniden başlatmanız gerekebilir.
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
@endsection
