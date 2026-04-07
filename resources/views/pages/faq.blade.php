@extends('layouts.app')

@section('title', 'Sık Sorulan Sorular - Spordosyam')

@push('styles')
<style>
.faq-hero { background: linear-gradient(160deg, {{ ($homepage_theme ?? 'theme_1') === 'theme_2' ? '#f0fdf4 0%, #ecfdf5 50%, #f0fdfa 100%' : '#f5f3ff 0%, #ede9fe 50%, #e0e7ff 100%' }}); }
.faq-item { transition: border-color 0.2s, background 0.2s; }
.faq-item:hover { {{ ($homepage_theme ?? 'theme_1') === 'theme_2' ? 'border-color: rgb(52 211 153); background: rgb(240 253 244);' : 'border-color: rgb(129 140 248); background: rgb(238 242 255);' }} }
.faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
.faq-answer.open { max-height: 500px; }
</style>
@endpush

@section('content')
@php
    $isT2 = ($homepage_theme ?? 'theme_1') === 'theme_2';
    $faqs = [
        'genel' => ['title' => 'Genel', 'items' => [
            ['q' => 'Spordosyam nedir?', 'a' => 'Spordosyam, spor okulları için geliştirilmiş kapsamlı bir yönetim sistemidir. Öğrenci yönetimi, ödeme takibi, yoklama sistemi ve veli iletişimi gibi tüm işlemleri tek platformda yönetmenizi sağlar.'],
            ['q' => 'Ücretsiz mi?', 'a' => 'Evet, demo hesabı ücretsizdir. Demo hesabı ile tüm özellikleri test edebilirsiniz. Detaylı fiyatlandırma bilgileri için bizimle iletişime geçin.'],
            ['q' => 'Verilerim güvende mi?', 'a' => 'Evet, tüm verileriniz SSL şifreleme ile korunmaktadır. Düzenli yedekleme yapıyoruz ve verileriniz güvenli sunucularda saklanmaktadır.'],
        ]],
        'kayit' => ['title' => 'Kayıt & Giriş', 'items' => [
            ['q' => 'Nasıl kayıt olabilirim?', 'a' => 'Ana sayfadaki "Demo Talep Et" butonuna tıklayarak başvuru formunu doldurun. Başvurunuz onaylandıktan sonra size e-posta ile giriş bilgileri gönderilecektir.'],
            ['q' => 'Şifremi unuttum, ne yapmalıyım?', 'a' => 'Giriş sayfasındaki "Şifremi Unuttum" linkine tıklayarak e-posta adresinize şifre sıfırlama bağlantısı gönderebilirsiniz.'],
        ]],
        'ozellikler' => ['title' => 'Özellikler', 'items' => [
            ['q' => 'Hangi özellikler mevcut?', 'a' => 'Öğrenci yönetimi ve takibi, otomatik ödeme takibi ve bildirimleri, yoklama sistemi, veli iletişim paneli, medya paylaşımı ve detaylı raporlama ile analitik.'],
            ['q' => 'Mobil uygulama var mı?', 'a' => 'Şu an için web tabanlı bir sistemdir ve tüm mobil cihazlarda sorunsuz çalışır. Mobil uygulama geliştirme çalışmalarımız devam etmektedir.'],
        ]],
        'odemeler' => ['title' => 'Ödemeler', 'items' => [
            ['q' => 'Hangi ödeme yöntemleri kabul ediliyor?', 'a' => 'Kredi kartı, banka kartı ve havale/EFT ile ödeme yapabilirsiniz. Tüm ödemeler Iyzico güvencesi altındadır.'],
            ['q' => 'Ödeme güvenli mi?', 'a' => 'Evet, tüm ödemeler SSL şifreleme ile korunmaktadır. Kart bilgileriniz sistemimizde saklanmaz ve Iyzico güvenli ödeme altyapısı kullanılmaktadır.'],
            ['q' => 'Fatura alabilir miyim?', 'a' => 'Evet, ödeme sonrası e-posta adresinize fatura gönderilir. Ayrıca veli panelinden tüm faturalarınızı görüntüleyip indirebilirsiniz.'],
        ]],
        'destek' => ['title' => 'Destek', 'items' => [
            ['q' => 'Teknik destek nasıl alınır?', 'a' => 'İletişim sayfasından bize ulaşabilir veya e-posta gönderebilirsiniz. Müşteri destek ekibimiz en kısa sürede size geri dönüş yapacaktır.'],
            ['q' => 'Eğitim materyalleri var mı?', 'a' => 'Evet, yardım merkezimizde detaylı kullanım kılavuzları ve video eğitimler bulunmaktadır.'],
        ]],
    ];
    $leftCategories = ['genel', 'kayit', 'ozellikler'];
    $rightCategories = ['odemeler', 'destek'];
@endphp
@include('partials.guest-nav')

<div class="min-h-screen {{ $isT2 ? 'bg-slate-50' : 'bg-gray-50' }}">
    {{-- Hero - full width --}}
    <section class="faq-hero pt-16 pb-12 md:pt-20 md:pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium {{ $isT2 ? 'bg-white/80 text-emerald-700 border border-emerald-200' : 'bg-white/80 text-indigo-700 border border-indigo-200' }} shadow-sm mb-6">
                <i class="fas fa-question-circle"></i>
                SSS
            </span>
            <h1 class="text-4xl md:text-5xl font-bold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-4 tracking-tight">
                Sık Sorulan Sorular
            </h1>
            <p class="text-lg {{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} max-w-2xl mx-auto">
                En çok merak edilen sorular ve cevapları tek yerde.
            </p>
        </div>
    </section>

    {{-- 2 sütun: kategori kartları yan yana --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-10">
            {{-- Sol sütun: Genel, Kayıt, Özellikler --}}
            <div class="space-y-8">
                @foreach($leftCategories as $catId)
                @php $cat = $faqs[$catId] ?? null; @endphp
                @if($cat)
                <div class="faq-category-card bg-white rounded-2xl shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }} overflow-hidden" id="{{ $catId }}">
                    <div class="px-6 py-4 border-b {{ $isT2 ? 'border-slate-100 bg-slate-50/50' : 'border-gray-100 bg-gray-50/50' }}">
                        <h2 class="text-lg font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }}">{{ $cat['title'] }}</h2>
                    </div>
                    <div class="p-4 space-y-2">
                        @foreach($cat['items'] as $idx => $faq)
                        <div class="faq-item rounded-xl border {{ $isT2 ? 'border-slate-200' : 'border-gray-200' }} overflow-hidden">
                            <button type="button" class="faq-trigger w-full flex items-center justify-between gap-4 text-left px-4 py-3.5 {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} font-medium text-sm" data-faq-id="faq-{{ $catId }}-{{ $idx }}" aria-expanded="false">
                                <span>{{ $faq['q'] }}</span>
                                <i class="fas fa-chevron-down text-xs {{ $isT2 ? 'text-emerald-500' : 'text-indigo-500' }} faq-icon transition-transform shrink-0"></i>
                            </button>
                            <div class="faq-answer" id="faq-{{ $catId }}-{{ $idx }}">
                                <div class="px-4 pb-3 pt-0 {{ $isT2 ? 'text-slate-600 border-t border-slate-100' : 'text-gray-600 border-t border-gray-100' }} text-sm leading-relaxed">
                                    <p class="pt-2">{{ $faq['a'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @endforeach
            </div>

            {{-- Sağ sütun: Ödemeler, Destek --}}
            <div class="space-y-8">
                @foreach($rightCategories as $catId)
                @php $cat = $faqs[$catId] ?? null; @endphp
                @if($cat)
                <div class="faq-category-card bg-white rounded-2xl shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }} overflow-hidden" id="{{ $catId }}">
                    <div class="px-6 py-4 border-b {{ $isT2 ? 'border-slate-100 bg-slate-50/50' : 'border-gray-100 bg-gray-50/50' }}">
                        <h2 class="text-lg font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }}">{{ $cat['title'] }}</h2>
                    </div>
                    <div class="p-4 space-y-2">
                        @foreach($cat['items'] as $idx => $faq)
                        <div class="faq-item rounded-xl border {{ $isT2 ? 'border-slate-200' : 'border-gray-200' }} overflow-hidden">
                            <button type="button" class="faq-trigger w-full flex items-center justify-between gap-4 text-left px-4 py-3.5 {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} font-medium text-sm" data-faq-id="faq-{{ $catId }}-{{ $idx }}" aria-expanded="false">
                                <span>{{ $faq['q'] }}</span>
                                <i class="fas fa-chevron-down text-xs {{ $isT2 ? 'text-emerald-500' : 'text-indigo-500' }} faq-icon transition-transform shrink-0"></i>
                            </button>
                            <div class="faq-answer" id="faq-{{ $catId }}-{{ $idx }}">
                                <div class="px-4 pb-3 pt-0 {{ $isT2 ? 'text-slate-600 border-t border-slate-100' : 'text-gray-600 border-t border-gray-100' }} text-sm leading-relaxed">
                                    <p class="pt-2">{{ $faq['a'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>

        {{-- CTA - full width --}}
        <div class="mt-14 rounded-2xl p-8 text-center {{ $isT2 ? 'bg-emerald-500/10 border border-emerald-200/60' : 'bg-indigo-500/10 border border-indigo-200/60' }}">
            <h3 class="text-xl font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-2">Cevabını bulamadınız mı?</h3>
            <p class="{{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} mb-6">Bizimle iletişime geçin, size yardımcı olalım.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('contact') }}" class="inline-flex items-center px-6 py-3 {{ $isT2 ? 'bg-emerald-500 hover:bg-emerald-600 text-white' : 'bg-indigo-600 hover:bg-indigo-700 text-white' }} rounded-xl font-medium transition-colors shadow-md">
                    <i class="fas fa-envelope mr-2"></i>İletişime Geç
                </a>
                <a href="{{ route('help') }}" class="inline-flex items-center px-6 py-3 bg-white {{ $isT2 ? 'border border-emerald-500 text-emerald-600 hover:bg-emerald-50' : 'border border-indigo-600 text-indigo-600 hover:bg-indigo-50' }} rounded-xl font-medium transition-colors">
                    <i class="fas fa-book mr-2"></i>Yardım Merkezi
                </a>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
document.querySelectorAll('.faq-trigger').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-faq-id');
        const answer = document.getElementById(id);
        const icon = this.querySelector('.faq-icon');
        const isOpen = answer && answer.classList.contains('open');
        document.querySelectorAll('.faq-answer').forEach(el => el.classList.remove('open'));
        document.querySelectorAll('.faq-icon').forEach(i => { i.style.transform = ''; });
        if (answer && !isOpen) {
            answer.classList.add('open');
            if (icon) icon.style.transform = 'rotate(180deg)';
        }
    });
});
</script>
@endpush
@endsection
