@extends('layouts.app')

@section('title', 'Spordosyam - Spor Okulları Yönetim Sistemi')

@push('styles')
<style>
    html { scroll-behavior: smooth; }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(24px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
    .hero-gradient { background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 40%, #f1f5f9 70%, #ddd6fe 100%); }
    .section-soft { background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); }
    .card-soft { box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 12px rgba(0,0,0,0.04); }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-800">
    {{-- Header - Hafif, renkli vurgu --}}
    <nav class="sticky top-0 z-50 bg-white/95 border-b border-slate-200/80 backdrop-blur-md shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <span class="text-white font-bold text-lg">S</span>
                    </div>
                    <span class="text-xl font-semibold text-slate-800">Spordosyam</span>
                </a>
                <div class="hidden md:flex items-center gap-8">
                    <a href="#ozellikler" class="text-sm font-medium text-slate-600 hover:text-emerald-600 transition-colors">Özellikler</a>
                    <a href="#nasil-calisir" class="text-sm font-medium text-slate-600 hover:text-emerald-600 transition-colors">Nasıl Çalışır</a>
                    <a href="#avantajlar" class="text-sm font-medium text-slate-600 hover:text-emerald-600 transition-colors">Avantajlar</a>
                    <a href="{{ route('contact') }}" class="text-sm font-medium text-slate-600 hover:text-emerald-600 transition-colors">İletişim</a>
                </div>
                <div class="flex items-center gap-3">
                    @auth
                        @php
                            $dashboardRoute = match(auth()->user()->role) {
                                'superadmin' => route('superadmin.dashboard'),
                                'admin' => route('admin.dashboard'),
                                'coach' => route('coach.dashboard'),
                                'parent' => route('parent.dashboard'),
                                default => route('home'),
                            };
                        @endphp
                        <a href="{{ $dashboardRoute }}" class="px-4 py-2 text-sm font-medium text-slate-600 border border-slate-300 rounded-xl hover:border-emerald-400 hover:text-emerald-600 transition-colors">Panel</a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all shadow-md">Çıkış Yap</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-emerald-600 transition-colors">Giriş Yap</a>
                        <a href="{{ route('register') }}" class="px-5 py-2 text-sm font-medium text-white bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all shadow-md">Demo Talep Et</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero: metin + illüstrasyon --}}
    <section class="hero-gradient relative overflow-hidden py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="order-2 lg:order-1">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-white/90 text-emerald-700 border border-emerald-200 shadow-sm mb-6">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        Spor Okulları İçin Özel Çözüm
                    </span>
                    <h1 class="text-4xl md:text-5xl font-bold text-slate-800 mb-6 leading-tight">
                        Spor Okullarınızı<br>
                        <span class="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">Dijitalleştirin</span>
                    </h1>
                    <p class="text-lg text-slate-600 leading-relaxed mb-8 max-w-xl">
                        Öğrenci yönetiminden ödeme takibine, yoklama sisteminden veli iletişimine kadar
                        <span class="font-semibold text-slate-700">tüm işlemlerinizi tek platformda</span> yönetin.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        @auth
                            @php $dashboardRoute = match(auth()->user()->role) { 'superadmin' => route('superadmin.dashboard'), 'admin' => route('admin.dashboard'), 'coach' => route('coach.dashboard'), 'parent' => route('parent.dashboard'), default => route('home'), }; @endphp
                            <a href="{{ $dashboardRoute }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-white bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl shadow-lg hover:shadow-xl transition-all">
                                <i class="fas fa-tachometer-alt mr-2"></i>Panele Git <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-slate-700 bg-white border-2 border-slate-200 rounded-xl hover:border-slate-300 transition-colors">Çıkış Yap</button>
                            </form>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-white bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl shadow-lg hover:shadow-xl transition-all">
                                Ücretsiz Demo Talep Et <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-slate-700 bg-white border-2 border-slate-200 rounded-xl hover:border-slate-300 transition-colors">Giriş Yap</a>
                        @endauth
                    </div>
                    <div class="mt-10 flex flex-wrap gap-6 text-sm text-slate-600">
                        <span class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> Ücretsiz Deneme</span>
                        <span class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> Kurulum Gerektirmez</span>
                        <span class="flex items-center gap-2"><i class="fas fa-check text-emerald-500"></i> 7/24 Destek</span>
                    </div>
                </div>
                <div class="order-1 lg:order-2 flex justify-center lg:justify-end">
                    <img src="https://illustrations.popsy.co/emerald/student-graduation.svg" alt="Spor ve eğitim" class="w-full max-w-md h-auto drop-shadow-lg" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('images/illustrations/hero-education.svg') }}';">
                </div>
            </div>
        </div>
    </section>

    {{-- Özellikler: renkli kartlar + illüstrasyonlar --}}
    <section id="ozellikler" class="section-soft py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-800 mb-3">
                    Güçlü <span class="text-emerald-600">Özellikler</span>
                </h2>
                <p class="text-slate-600 max-w-xl mx-auto">Spor okulunuzun tüm ihtiyaçlarını karşılayan kapsamlı yönetim çözümü</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-6 card-soft border border-slate-100 hover:border-emerald-200 transition-all group">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <i class="fas fa-users text-emerald-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Öğrenci Yönetimi</h3>
                    <p class="text-sm text-slate-600">Öğrencilerinizi kaydedin, sınıflara atayın ve tüm kayıtları tek yerden yönetin.</p>
                </div>
                <div class="bg-white rounded-2xl p-6 card-soft border border-slate-100 hover:border-violet-200 transition-all group">
                    <div class="w-14 h-14 bg-gradient-to-br from-violet-100 to-purple-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <i class="fas fa-credit-card text-violet-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Aidat & Ödeme</h3>
                    <p class="text-sm text-slate-600">Aidat planları, ödeme takibi ve Iyzico ile güvenli online ödeme.</p>
                </div>
                <div class="bg-white rounded-2xl p-6 card-soft border border-slate-100 hover:border-sky-200 transition-all group">
                    <div class="w-14 h-14 bg-gradient-to-br from-sky-100 to-cyan-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <i class="fas fa-clipboard-check text-sky-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Yoklama Sistemi</h3>
                    <p class="text-sm text-slate-600">Ders yoklamaları, devamsızlık takibi ve veli bildirimleri.</p>
                </div>
                <div class="bg-white rounded-2xl p-6 card-soft border border-slate-100 hover:border-amber-200 transition-all group">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-100 to-orange-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <i class="fas fa-user-friends text-amber-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Veli Paneli</h3>
                    <p class="text-sm text-slate-600">Veliler yoklama, gelişim ve ödemeleri takip edebilir.</p>
                </div>
                <div class="bg-white rounded-2xl p-6 card-soft border border-slate-100 hover:border-pink-200 transition-all group">
                    <div class="w-14 h-14 bg-gradient-to-br from-pink-100 to-rose-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <i class="fas fa-photo-video text-pink-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Medya Paylaşımı</h3>
                    <p class="text-sm text-slate-600">Fotoğraf, video ve belgeleri sınıf veya okulla paylaşın.</p>
                </div>
                <div class="bg-white rounded-2xl p-6 card-soft border border-slate-100 hover:border-indigo-200 transition-all group">
                    <div class="w-14 h-14 bg-gradient-to-br from-indigo-100 to-blue-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <i class="fas fa-chart-bar text-indigo-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Raporlama</h3>
                    <p class="text-sm text-slate-600">Detaylı raporlar ve analitiklerle performans takibi.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Nasıl Çalışır: illüstrasyon + 3 adım --}}
    <section id="nasil-calisir" class="py-20 bg-gradient-to-b from-slate-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                @include('home-themes.partials.illustration-how-it-works-sport')
                <div style="display:none" aria-hidden="true">
                    <svg class="w-full max-w-sm h-auto" viewBox="0 0 420 380" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img">
                        <defs>
                            <linearGradient id="theme2-hiw-bg" x1="0" y1="0" x2="420" y2="380" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#fffbeb"/><stop offset="0.5" stop-color="#fef3c7"/><stop offset="1" stop-color="#fde68a"/>
                            </linearGradient>
                            <linearGradient id="theme2-hiw-1" x1="80" y1="60" x2="160" y2="140" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#10b981"/><stop offset="1" stop-color="#059669"/>
                            </linearGradient>
                            <linearGradient id="theme2-hiw-2" x1="200" y1="60" x2="280" y2="140" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#8b5cf6"/><stop offset="1" stop-color="#7c3aed"/>
                            </linearGradient>
                            <linearGradient id="theme2-hiw-3" x1="320" y1="60" x2="400" y2="140" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#0ea5e9"/><stop offset="1" stop-color="#0284c7"/>
                            </linearGradient>
                            <filter id="theme2-hiw-shadow" x="-10%" y="-10%" width="120%" height="120%">
                                <feDropShadow dx="0" dy="4" stdDeviation="6" flood-opacity="0.15"/>
                            </filter>
                        </defs>
                        <rect width="420" height="380" rx="24" fill="url(#theme2-hiw-bg)" opacity="0.8"/>
                        <circle cx="210" cy="260" r="65" fill="#fde68a"/><circle cx="210" cy="255" r="55" fill="#fef3c7"/>
                        <ellipse cx="198" cy="252" rx="8" ry="10" fill="#1e293b"/><ellipse cx="222" cy="252" rx="8" ry="10" fill="#1e293b"/>
                        <path d="M195 272 Q210 280 225 272" stroke="#1e293b" stroke-width="2" fill="none" stroke-linecap="round"/>
                        <path d="M160 318 Q160 380 210 385 Q260 380 260 318 L260 310 Q260 295 210 295 Q160 295 160 310 Z" fill="#fcd34d" filter="url(#theme2-hiw-shadow)"/>
                        <rect x="100" y="320" width="220" height="12" rx="6" fill="#b45309"/><rect x="110" y="332" width="200" height="8" rx="2" fill="#92400e"/>
                        <rect x="155" y="255" width="110" height="65" rx="8" fill="#1e293b" filter="url(#theme2-hiw-shadow)"/>
                        <rect x="165" y="265" width="90" height="45" rx="4" fill="#38bdf8"/>
                        <circle cx="120" cy="100" r="45" fill="url(#theme2-hiw-1)" filter="url(#theme2-hiw-shadow)"/>
                        <text x="120" y="108" text-anchor="middle" fill="white" font-weight="bold" font-size="28" font-family="system-ui,sans-serif">1</text>
                        <path d="M120 155 L120 200 M105 185 L120 200 L135 185" stroke="#059669" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="210" cy="100" r="45" fill="url(#theme2-hiw-2)" filter="url(#theme2-hiw-shadow)"/>
                        <text x="210" y="108" text-anchor="middle" fill="white" font-weight="bold" font-size="28" font-family="system-ui,sans-serif">2</text>
                        <path d="M210 155 L210 220 M195 205 L210 220 L225 205" stroke="#7c3aed" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="300" cy="100" r="45" fill="url(#theme2-hiw-3)" filter="url(#theme2-hiw-shadow)"/>
                        <text x="300" y="108" text-anchor="middle" fill="white" font-weight="bold" font-size="28" font-family="system-ui,sans-serif">3</text>
                        <path d="M300 155 L300 200 M285 185 L300 200 L315 185" stroke="#0284c7" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M165 100 L195 100 M225 100 L255 100" stroke="#f59e0b" stroke-width="2" stroke-dasharray="6 4" fill="none" opacity="0.8"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-800 mb-3">
                        Nasıl <span class="text-emerald-600">Çalışır?</span>
                    </h2>
                    <p class="text-slate-600 mb-10">Sadece 3 adımda başlayın</p>
                    <div class="space-y-6">
                        <div class="flex gap-4 items-start">
                            <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0 shadow-md">1</div>
                            <div>
                                <h3 class="font-bold text-slate-800 mb-1">Demo Talep Edin</h3>
                                <p class="text-sm text-slate-600">Ücretsiz demo için başvurun, tüm özellikleri deneyin.</p>
                            </div>
                        </div>
                        <div class="flex gap-4 items-start">
                            <div class="w-12 h-12 bg-violet-500 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0 shadow-md">2</div>
                            <div>
                                <h3 class="font-bold text-slate-800 mb-1">Hesabınızı Oluşturun</h3>
                                <p class="text-sm text-slate-600">Şube, branş ve sınıflarınızı ekleyin.</p>
                            </div>
                        </div>
                        <div class="flex gap-4 items-start">
                            <div class="w-12 h-12 bg-sky-500 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0 shadow-md">3</div>
                            <div>
                                <h3 class="font-bold text-slate-800 mb-1">Yönetmeye Başlayın</h3>
                                <p class="text-sm text-slate-600">Öğrenci ve antrenörlerinizi ekleyin, işlemlerinizi dijitalleştirin.</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-10">
                        <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl shadow-md hover:shadow-lg transition-all">
                            Hemen Başlayın <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Avantajlar: renkli ikonlar --}}
    <section id="avantajlar" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-800 mb-3">
                    Neden <span class="text-emerald-600">Spordosyam?</span>
                </h2>
                <p class="text-slate-600 max-w-xl mx-auto">Spor okulunuzu dijitalleştirmek için en iyi seçim</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 transition-colors">
                    <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-bolt text-emerald-600 text-xl"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 mb-1">Hızlı & Kolay</h3>
                    <p class="text-xs text-slate-600">5 dakikada kurulum</p>
                </div>
                <div class="text-center p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-violet-200 transition-colors">
                    <div class="w-14 h-14 bg-violet-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-shield-alt text-violet-600 text-xl"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 mb-1">Güvenli</h3>
                    <p class="text-xs text-slate-600">SSL ve güvenli ödeme</p>
                </div>
                <div class="text-center p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-sky-200 transition-colors">
                    <div class="w-14 h-14 bg-sky-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-mobile-alt text-sky-600 text-xl"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 mb-1">Mobil Uyumlu</h3>
                    <p class="text-xs text-slate-600">Her cihazdan erişim</p>
                </div>
                <div class="text-center p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-amber-200 transition-colors">
                    <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-headset text-amber-600 text-xl"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 mb-1">7/24 Destek</h3>
                    <p class="text-xs text-slate-600">Uzman ekip</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA: gradient + illüstrasyon --}}
    <section class="py-20 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-80 h-80 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
        </div>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="grid md:grid-cols-2 gap-10 items-center">
                <div class="text-center md:text-left">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                        Hemen Başlayın, Ücretsiz Deneyin
                    </h2>
                    <p class="text-emerald-50 mb-8 max-w-md">
                        Demo hesabınızı oluşturun ve tüm özellikleri ücretsiz deneyin.
                    </p>
                    @auth
                        @php $dashboardRoute = match(auth()->user()->role) { 'superadmin' => route('superadmin.dashboard'), 'admin' => route('admin.dashboard'), 'coach' => route('coach.dashboard'), 'parent' => route('parent.dashboard'), default => route('home'), }; @endphp
                        <a href="{{ $dashboardRoute }}" class="inline-flex items-center px-8 py-4 text-base font-semibold text-emerald-900 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all">
                            <i class="fas fa-tachometer-alt mr-2"></i>Panele Git
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 text-base font-semibold text-emerald-900 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all">
                            Ücretsiz Demo Talep Et <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    @endauth
                </div>
                <div class="flex justify-center md:justify-end">
                    {{-- Spor okulu: kupa ve kutlama --}}
                    <svg class="w-full max-w-xs h-auto opacity-95" viewBox="0 0 220 280" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <defs>
                            <linearGradient id="theme2-trophy" x1="110" y1="260" x2="110" y2="40" gradientUnits="userSpaceOnUse"><stop stop-color="#ffffff"/><stop offset="0.4" stop-color="#fef3c7"/><stop offset="1" stop-color="#fde68a"/></linearGradient>
                            <filter id="theme2-cta-glow" x="-20%" y="-20%" width="140%" height="140%"><feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="#fff" flood-opacity="0.4"/></filter>
                        </defs>
                        {{-- Konfeti --}}
                        <circle cx="50" cy="60" r="6" fill="#fef3c7"/><circle cx="170" cy="80" r="5" fill="#fde68a"/>
                        <circle cx="80" cy="200" r="4" fill="#fff" opacity="0.9"/><circle cx="140" cy="180" r="5" fill="#fef3c7"/>
                        {{-- Kupa gövdesi --}}
                        <path d="M70 100 L70 200 L50 240 L50 255 L170 255 L170 240 L150 200 L150 100 L130 60 L90 60 Z" fill="url(#theme2-trophy)" stroke="#fcd34d" stroke-width="2" filter="url(#theme2-cta-glow)"/>
                        <ellipse cx="110" cy="100" rx="40" ry="8" fill="#fef9c3" opacity="0.9"/>
                        {{-- Kupa kulpları --}}
                        <path d="M70 75 Q40 75 40 110 Q40 140 70 100" stroke="#fde68a" stroke-width="8" fill="none" stroke-linecap="round"/>
                        <path d="M150 75 Q180 75 180 110 Q180 140 150 100" stroke="#fde68a" stroke-width="8" fill="none" stroke-linecap="round"/>
                        {{-- Yıldız / başarı --}}
                        <path d="M110 45 L113 58 L126 58 L116 66 L120 78 L110 71 L100 78 L104 66 L94 58 L107 58 Z" fill="#ffffff"/>
                        {{-- Spor topu (kupanın yanında) --}}
                        <circle cx="175" cy="220" r="22" fill="#fbbf24" stroke="#f59e0b" stroke-width="2"/>
                        <path d="M162 215 Q175 220 188 215" stroke="#f59e0b" stroke-width="2" fill="none" stroke-linecap="round"/>
                        {{-- Küçük atlet silüeti (kutlama) --}}
                        <circle cx="45" cy="230" r="14" fill="#fff" opacity="0.95"/>
                        <path d="M38 245 L45 255 L52 245" stroke="#fef3c7" stroke-width="3" fill="none" stroke-linecap="round"/>
                        <path d="M35 238 L55 238" stroke="#fde68a" stroke-width="2" fill="none"/>
                    </svg>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
</script>
@endpush
@endsection
