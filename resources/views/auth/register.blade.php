@extends('layouts.app')

@section('title', 'Demo Talep Et')

@section('content')
@php $isT2 = ($homepage_theme ?? 'theme_1') === 'theme_2'; @endphp
<div class="min-h-screen {{ $isT2 ? 'bg-slate-50' : 'bg-gray-50' }}">
    @include('partials.header')

    <div class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full">
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 {{ $isT2 ? 'bg-gradient-to-br from-emerald-500 to-teal-600' : 'bg-indigo-600' }} rounded-xl mb-6 shadow-md">
                    <i class="fas fa-dumbbell text-white text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-2">Spordosyam</h1>
                <p class="{{ $isT2 ? 'text-slate-600' : 'text-gray-500' }} text-sm">Spor okulları için yönetim sistemi</p>
            </div>

        <div class="bg-white {{ $isT2 ? 'rounded-2xl shadow-sm border border-slate-200' : 'rounded-lg shadow-sm border border-gray-200' }}">
            <div class="px-8 py-6 border-b {{ $isT2 ? 'border-slate-200' : 'border-gray-200' }}">
                <h2 class="text-xl font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }}">Demo Talep Formu</h2>
                <p class="text-sm {{ $isT2 ? 'text-slate-600' : 'text-gray-500' }} mt-1">Spordosyam sistemini denemek için formu doldurun</p>
            </div>
            
            <form id="registerForm" class="p-8 space-y-5" action="{{ route('register') }}" method="POST">
                @csrf
                
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-lg">
                        <p class="font-medium">Başarılı!</p>
                        <p class="text-sm mt-1">{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->has('csrf'))
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg text-sm">
                        Oturum süreniz dolmuş. Lütfen tekrar deneyin.
                    </div>
                @endif

                <!-- School Name & Contact Name -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="school_name" class="block text-sm font-medium {{ $isT2 ? 'text-slate-700' : 'text-gray-700' }} mb-2">
                            Spor Okulu Adı <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="school_name" id="school_name" required
                               class="block w-full px-4 py-3 border {{ $isT2 ? 'border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' : 'border-gray-300 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500' }} transition-colors @error('school_name') border-red-500 @enderror"
                               placeholder="Örn: Spor Akademisi" value="{{ old('school_name') }}">
                        @error('school_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_name" class="block text-sm font-medium {{ $isT2 ? 'text-slate-700' : 'text-gray-700' }} mb-2">
                            İletişim Kişisi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="contact_name" id="contact_name" required
                               class="block w-full px-4 py-3 border {{ $isT2 ? 'border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' : 'border-gray-300 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500' }} transition-colors @error('contact_name') border-red-500 @enderror"
                               placeholder="Ad Soyad" value="{{ old('contact_name') }}">
                        @error('contact_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email & Phone -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="email" class="block text-sm font-medium {{ $isT2 ? 'text-slate-700' : 'text-gray-700' }} mb-2">
                            E-posta Adresi <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" required
                               class="block w-full px-4 py-3 border {{ $isT2 ? 'border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' : 'border-gray-300 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500' }} transition-colors @error('email') border-red-500 @enderror"
                               placeholder="ornek@spordosyam.com" value="{{ old('email') }}">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium {{ $isT2 ? 'text-slate-700' : 'text-gray-700' }} mb-2">
                            Telefon <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" id="phone" required
                               class="block w-full px-4 py-3 border {{ $isT2 ? 'border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' : 'border-gray-300 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500' }} transition-colors @error('phone') border-red-500 @enderror"
                               placeholder="0 (5XX) XXX XX XX" value="{{ old('phone') }}">
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium {{ $isT2 ? 'text-slate-700' : 'text-gray-700' }} mb-2">
                        Adres
                    </label>
                    <textarea name="address" id="address" rows="3"
                              class="block w-full px-4 py-3 border {{ $isT2 ? 'border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' : 'border-gray-300 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500' }} transition-colors resize-none @error('address') border-red-500 @enderror"
                              placeholder="Spor okulunuzun adresi">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message -->
                <div>
                    <label for="message" class="block text-sm font-medium {{ $isT2 ? 'text-slate-700' : 'text-gray-700' }} mb-2">
                        Mesaj
                    </label>
                    <textarea name="message" id="message" rows="4"
                              class="block w-full px-4 py-3 border {{ $isT2 ? 'border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' : 'border-gray-300 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500' }} transition-colors resize-none @error('message') border-red-500 @enderror"
                              placeholder="Eklemek istediğiniz notlar veya sorularınız...">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password (demo onaylandığında giriş için kullanılacak) -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="block text-sm font-medium {{ $isT2 ? 'text-slate-700' : 'text-gray-700' }} mb-2">
                            Şifre <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" id="password" required minlength="8"
                               class="block w-full px-4 py-3 border {{ $isT2 ? 'border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' : 'border-gray-300 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500' }} transition-colors @error('password') border-red-500 @enderror"
                               placeholder="En az 8 karakter" autocomplete="new-password">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium {{ $isT2 ? 'text-slate-700' : 'text-gray-700' }} mb-2">
                            Şifre Tekrar <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                               class="block w-full px-4 py-3 border {{ $isT2 ? 'border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' : 'border-gray-300 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500' }} transition-colors"
                               placeholder="Şifrenizi tekrar girin" autocomplete="new-password">
                    </div>
                </div>
                <p class="text-xs text-gray-500 -mt-2">Demo talebiniz onaylandığında bu şifre ile panele giriş yapabilirsiniz.</p>

                <!-- Submit Button -->
                <div>
                    <button type="submit" id="registerButton"
                            class="w-full flex items-center justify-center py-3 px-4 border border-transparent {{ $isT2 ? 'rounded-xl text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 focus:ring-emerald-500' : 'rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500' }} text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="buttonText">Demo Talebi Gönder</span>
                        <svg id="buttonSpinner" class="hidden w-5 h-5 ml-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Login Link -->
                <div class="text-center pt-4 border-t {{ $isT2 ? 'border-slate-200' : 'border-gray-200' }}">
                    <p class="text-sm {{ $isT2 ? 'text-slate-600' : 'text-gray-600' }}">
                        Zaten hesabınız var mı?
                        <a href="{{ route('login') }}" class="font-medium {{ $isT2 ? 'text-emerald-600 hover:text-emerald-700' : 'text-indigo-600 hover:text-indigo-500' }}">
                            Giriş yapın
                        </a>
                    </p>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const button = document.getElementById('registerButton');
    const buttonText = document.getElementById('buttonText');
    const buttonSpinner = document.getElementById('buttonSpinner');
    
    if (form && button) {
        form.addEventListener('submit', function(e) {
            // Butonu devre dışı bırak ve loading göster
            button.disabled = true;
            buttonText.textContent = 'Gönderiliyor...';
            buttonSpinner.classList.remove('hidden');
            
            // Eğer form zaten submit edilmişse, tekrar submit etme
            if (form.dataset.submitting === 'true') {
                e.preventDefault();
                return false;
            }
            
            form.dataset.submitting = 'true';
        });
    }
    
    // CSRF token'ı otomatik yenile (her 5 dakikada bir)
    setInterval(function() {
        fetch('{{ route("register") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newToken = doc.querySelector('input[name="_token"]');
            if (newToken && form) {
                const currentToken = form.querySelector('input[name="_token"]');
                if (currentToken) {
                    currentToken.value = newToken.value;
                }
            }
        })
        .catch(err => console.log('CSRF token yenileme hatası:', err));
    }, 5 * 60 * 1000); // 5 dakika
});
</script>
@endpush
@endsection
