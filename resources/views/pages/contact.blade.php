@extends('layouts.app')

@section('title', 'İletişim - Spordosyam')

@push('styles')
<style>
.page-hero-contact { background: linear-gradient(160deg, {{ ($homepage_theme ?? 'theme_1') === 'theme_2' ? '#f0fdf4 0%, #ecfdf5 50%, #f0fdfa 100%' : '#f5f3ff 0%, #ede9fe 50%, #e0e7ff 100%' }}); }
.contact-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.contact-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -8px rgba(0,0,0,0.08), 0 4px 12px -4px rgba(0,0,0,0.04); }
.quick-link-card { transition: transform 0.2s ease, border-color 0.2s ease; }
.quick-link-card:hover { transform: translateX(4px); }
</style>
@endpush

@section('content')
@php $isT2 = ($homepage_theme ?? 'theme_1') === 'theme_2'; @endphp
@include('partials.guest-nav')

<div class="min-h-screen {{ $isT2 ? 'bg-slate-50' : 'bg-gray-50' }}">
    {{-- Hero --}}
    <section class="page-hero-contact pt-16 pb-12 md:pt-20 md:pb-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium {{ $isT2 ? 'bg-white/80 text-emerald-700 border border-emerald-200' : 'bg-white/80 text-indigo-700 border border-indigo-200' }} shadow-sm mb-6">
                <i class="fas fa-paper-plane"></i>
                Bize ulaşın
            </span>
            <h1 class="text-4xl md:text-5xl font-bold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-4 tracking-tight">
                İletişim
            </h1>
            <p class="text-lg {{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} max-w-xl mx-auto">
                Sorularınız için buradayız. En kısa sürede size dönüş yapıyoruz.
            </p>
        </div>
    </section>

    {{-- Contact cards --}}
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @if($footerEmail)
            <a href="mailto:{{ $footerEmail }}" class="contact-card block bg-white rounded-2xl p-6 md:p-8 shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }} text-left group">
                <div class="w-14 h-14 rounded-2xl {{ $isT2 ? 'bg-emerald-100 text-emerald-600' : 'bg-indigo-100 text-indigo-600' }} flex items-center justify-center mb-5 group-hover:scale-105 transition-transform">
                    <i class="fas fa-envelope text-xl"></i>
                </div>
                <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-1">E-posta</h3>
                <p class="text-sm {{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} break-all">{{ $footerEmail }}</p>
                <span class="inline-flex items-center mt-4 text-sm font-medium {{ $isT2 ? 'text-emerald-600' : 'text-indigo-600' }} opacity-0 group-hover:opacity-100 transition-opacity">
                    E-posta gönder <i class="fas fa-arrow-right ml-1 text-xs"></i>
                </span>
            </a>
            @endif
            @if($footerPhone)
            <a href="tel:{{ $footerPhone }}" class="contact-card block bg-white rounded-2xl p-6 md:p-8 shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }} text-left group">
                <div class="w-14 h-14 rounded-2xl {{ $isT2 ? 'bg-emerald-100 text-emerald-600' : 'bg-indigo-100 text-indigo-600' }} flex items-center justify-center mb-5 group-hover:scale-105 transition-transform">
                    <i class="fas fa-phone text-xl"></i>
                </div>
                <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-1">Telefon</h3>
                <p class="text-sm {{ $isT2 ? 'text-slate-600' : 'text-gray-600' }}">{{ $footerPhone }}</p>
                <span class="inline-flex items-center mt-4 text-sm font-medium {{ $isT2 ? 'text-emerald-600' : 'text-indigo-600' }} opacity-0 group-hover:opacity-100 transition-opacity">
                    Hemen ara <i class="fas fa-arrow-right ml-1 text-xs"></i>
                </span>
            </a>
            @endif
            @if($footerAddress)
            <div class="contact-card bg-white rounded-2xl p-6 md:p-8 shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }} text-left">
                <div class="w-14 h-14 rounded-2xl {{ $isT2 ? 'bg-emerald-100 text-emerald-600' : 'bg-indigo-100 text-indigo-600' }} flex items-center justify-center mb-5">
                    <i class="fas fa-map-marker-alt text-xl"></i>
                </div>
                <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-1">Adres</h3>
                <p class="text-sm {{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} leading-relaxed">{{ $footerAddress }}</p>
            </div>
            @endif
        </div>
    </section>

    {{-- Working hours + Quick links --}}
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Çalışma saatleri --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl p-6 shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }}">
                    <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-4 flex items-center gap-2">
                        <i class="fas fa-clock {{ $isT2 ? 'text-emerald-500' : 'text-indigo-500' }}"></i>
                        Çalışma saatleri
                    </h3>
                    <ul class="space-y-3 text-sm {{ $isT2 ? 'text-slate-600' : 'text-gray-600' }}">
                        <li class="flex justify-between"><span>Pazartesi – Cuma</span><span class="font-medium">09:00 – 18:00</span></li>
                        <li class="flex justify-between"><span>Cumartesi</span><span class="font-medium">10:00 – 16:00</span></li>
                        <li class="flex justify-between"><span>Pazar</span><span class="font-medium">Kapalı</span></li>
                    </ul>
                </div>
            </div>

            {{-- Hızlı linkler --}}
            <div class="lg:col-span-2 space-y-4">
                <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-3 text-sm uppercase tracking-wider {{ $isT2 ? 'text-slate-500' : 'text-gray-500' }}">Daha fazla bilgi</h3>
                <a href="{{ route('faq') }}" class="quick-link-card flex items-center justify-between bg-white rounded-2xl p-5 shadow-sm border {{ $isT2 ? 'border-slate-100 hover:border-emerald-200' : 'border-gray-100 hover:border-indigo-200' }}">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl {{ $isT2 ? 'bg-emerald-50 text-emerald-600' : 'bg-indigo-50 text-indigo-600' }} flex items-center justify-center">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="text-left">
                            <h4 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }}">Sık Sorulan Sorular</h4>
                            <p class="text-sm {{ $isT2 ? 'text-slate-500' : 'text-gray-500' }}">En çok sorulan sorular ve cevaplar</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right {{ $isT2 ? 'text-emerald-400' : 'text-indigo-400' }}"></i>
                </a>
                <a href="{{ route('help') }}" class="quick-link-card flex items-center justify-between bg-white rounded-2xl p-5 shadow-sm border {{ $isT2 ? 'border-slate-100 hover:border-emerald-200' : 'border-gray-100 hover:border-indigo-200' }}">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl {{ $isT2 ? 'bg-emerald-50 text-emerald-600' : 'bg-indigo-50 text-indigo-600' }} flex items-center justify-center">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="text-left">
                            <h4 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }}">Yardım Merkezi</h4>
                            <p class="text-sm {{ $isT2 ? 'text-slate-500' : 'text-gray-500' }}">Kullanım kılavuzu ve rehberler</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right {{ $isT2 ? 'text-emerald-400' : 'text-indigo-400' }}"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- Support promise strip --}}
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="rounded-2xl p-8 {{ $isT2 ? 'bg-emerald-500/10 border border-emerald-200/60' : 'bg-indigo-500/10 border border-indigo-200/60' }}">
            <div class="flex flex-wrap items-center justify-center gap-x-12 gap-y-6 text-center">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle {{ $isT2 ? 'text-emerald-500' : 'text-indigo-500' }} text-xl"></i>
                    <span class="{{ $isT2 ? 'text-slate-700' : 'text-gray-700' }} font-medium">7/24 e-posta desteği</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle {{ $isT2 ? 'text-emerald-500' : 'text-indigo-500' }} text-xl"></i>
                    <span class="{{ $isT2 ? 'text-slate-700' : 'text-gray-700' }} font-medium">Hızlı yanıt</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle {{ $isT2 ? 'text-emerald-500' : 'text-indigo-500' }} text-xl"></i>
                    <span class="{{ $isT2 ? 'text-slate-700' : 'text-gray-700' }} font-medium">Uzman ekip</span>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
