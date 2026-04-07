@extends('layouts.app')

@section('title', 'Yardım Merkezi - Spordosyam')

@push('styles')
<style>
.help-hero { background: linear-gradient(160deg, {{ ($homepage_theme ?? 'theme_1') === 'theme_2' ? '#f0fdf4 0%, #ecfdf5 50%, #f0fdfa 100%' : '#f5f3ff 0%, #ede9fe 50%, #e0e7ff 100%' }}); }
.help-sidebar a.active { {{ ($homepage_theme ?? 'theme_1') === 'theme_2' ? 'background: rgb(236 253 245); color: rgb(5 150 105); font-weight: 600; border-left-color: rgb(16 185 129);' : 'background: rgb(238 242 255); color: rgb(99 102 241); font-weight: 600; border-left-color: rgb(99 102 241);' }} }
.help-step { transition: box-shadow 0.2s, transform 0.2s; }
.help-step:hover { box-shadow: 0 8px 24px -8px rgba(0,0,0,0.08); }
</style>
@endpush

@section('content')
@php $isT2 = ($homepage_theme ?? 'theme_1') === 'theme_2'; @endphp
@include('partials.guest-nav')

<div class="min-h-screen {{ $isT2 ? 'bg-slate-50' : 'bg-gray-50' }}">
    {{-- Hero --}}
    <section class="help-hero pt-16 pb-12 md:pt-20 md:pb-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium {{ $isT2 ? 'bg-white/80 text-emerald-700 border border-emerald-200' : 'bg-white/80 text-indigo-700 border border-indigo-200' }} shadow-sm mb-6">
                <i class="fas fa-book-open"></i>
                Yardım Merkezi
            </span>
            <h1 class="text-4xl md:text-5xl font-bold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-4 tracking-tight">
                Nasıl yapılır?
            </h1>
            <p class="text-lg {{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} max-w-xl mx-auto">
                Spordosyam kullanım rehberi ve sık karşılaşılan sorunların çözümleri.
            </p>
        </div>
    </section>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col lg:flex-row gap-10">
            {{-- Sidebar nav --}}
            <aside class="lg:w-56 shrink-0">
                <nav class="lg:sticky lg:top-24 space-y-1 help-sidebar">
                    <a href="#baslangic" class="help-nav block px-4 py-3 rounded-xl text-sm font-medium {{ $isT2 ? 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} border-l-4 border-transparent transition-colors">Başlangıç</a>
                    <a href="#ogrenci" class="help-nav block px-4 py-3 rounded-xl text-sm font-medium {{ $isT2 ? 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} border-l-4 border-transparent transition-colors">Öğrenci Yönetimi</a>
                    <a href="#odeme" class="help-nav block px-4 py-3 rounded-xl text-sm font-medium {{ $isT2 ? 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} border-l-4 border-transparent transition-colors">Ödeme İşlemleri</a>
                    <a href="#yoklama" class="help-nav block px-4 py-3 rounded-xl text-sm font-medium {{ $isT2 ? 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} border-l-4 border-transparent transition-colors">Yoklama Sistemi</a>
                    <a href="#destek" class="help-nav block px-4 py-3 rounded-xl text-sm font-medium {{ $isT2 ? 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }} border-l-4 border-transparent transition-colors">Destek</a>
                </nav>
            </aside>

            {{-- Content --}}
            <main class="flex-1 min-w-0 space-y-14">
                {{-- Başlangıç --}}
                <section id="baslangic" class="scroll-mt-28">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl {{ $isT2 ? 'bg-emerald-100 text-emerald-600' : 'bg-indigo-100 text-indigo-600' }} flex items-center justify-center">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h2 class="text-2xl font-bold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }}">Başlangıç</h2>
                    </div>
                    <div class="space-y-4">
                        <div class="help-step bg-white rounded-2xl p-6 shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }}">
                            <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-2">Sisteme nasıl kayıt olabilirim?</h3>
                            <p class="{{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} text-sm leading-relaxed">Ana sayfadaki "Demo Talep Et" butonuna tıklayarak demo hesabı talep edebilirsiniz. Başvurunuz onaylandıktan sonra size e-posta ile giriş bilgileri gönderilecektir.</p>
                        </div>
                        <div class="help-step bg-white rounded-2xl p-6 shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }}">
                            <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-2">Hangi tarayıcıları destekliyorsunuz?</h3>
                            <p class="{{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} text-sm leading-relaxed">Spordosyam, Chrome, Firefox, Safari ve Edge'in son sürümlerini desteklemektedir. En iyi deneyim için Chrome kullanmanızı öneririz.</p>
                        </div>
                        <div class="help-step bg-white rounded-2xl p-6 shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }}">
                            <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-2">Mobil uyumlu mu?</h3>
                            <p class="{{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} text-sm leading-relaxed">Evet, Spordosyam tamamen responsive tasarıma sahiptir ve tüm mobil cihazlarda sorunsuz çalışır.</p>
                        </div>
                    </div>
                </section>

                {{-- Öğrenci Yönetimi --}}
                <section id="ogrenci" class="scroll-mt-28">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl {{ $isT2 ? 'bg-emerald-100 text-emerald-600' : 'bg-indigo-100 text-indigo-600' }} flex items-center justify-center">
                            <i class="fas fa-users"></i>
                        </div>
                        <h2 class="text-2xl font-bold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }}">Öğrenci Yönetimi</h2>
                    </div>
                    <div class="space-y-4">
                        <div class="help-step bg-white rounded-2xl p-6 shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }}">
                            <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-2">Öğrenci nasıl eklenir?</h3>
                            <p class="{{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} text-sm leading-relaxed">Admin panelinden "Öğrenciler" menüsüne gidin ve "Yeni Öğrenci" butonuna tıklayın. Gerekli bilgileri doldurup kaydedin.</p>
                        </div>
                        <div class="help-step bg-white rounded-2xl p-6 shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }}">
                            <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-2">Öğrenci bilgileri nasıl güncellenir?</h3>
                            <p class="{{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} text-sm leading-relaxed">Öğrenci listesinden güncellemek istediğiniz öğrenciyi bulun ve "Düzenle" butonuna tıklayın. Değişiklikleri yaptıktan sonra kaydedin.</p>
                        </div>
                        <div class="help-step bg-white rounded-2xl p-6 shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }}">
                            <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-2">Öğrenci sınıfa nasıl atanır?</h3>
                            <p class="{{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} text-sm leading-relaxed">Öğrenci düzenleme sayfasında "Sınıf" alanından uygun sınıfı seçin ve kaydedin.</p>
                        </div>
                    </div>
                </section>

                {{-- Ödeme İşlemleri --}}
                <section id="odeme" class="scroll-mt-28">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl {{ $isT2 ? 'bg-emerald-100 text-emerald-600' : 'bg-indigo-100 text-indigo-600' }} flex items-center justify-center">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h2 class="text-2xl font-bold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }}">Ödeme İşlemleri</h2>
                    </div>
                    <div class="space-y-4">
                        <div class="help-step bg-white rounded-2xl p-6 shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }}">
                            <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-2">Ödeme nasıl yapılır?</h3>
                            <p class="{{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} text-sm leading-relaxed">Veli panelinden "Ödemeler" bölümüne gidin, ödeme yapmak istediğiniz aidatı seçin ve güvenli ödeme sayfasına yönlendirileceksiniz.</p>
                        </div>
                        <div class="help-step bg-white rounded-2xl p-6 shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }}">
                            <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-2">Hangi ödeme yöntemleri kabul ediliyor?</h3>
                            <p class="{{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} text-sm leading-relaxed">Kredi kartı, banka kartı ve havale/EFT ile ödeme yapabilirsiniz. Tüm ödemeler Iyzico güvencesi altındadır.</p>
                        </div>
                        <div class="help-step bg-white rounded-2xl p-6 shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }}">
                            <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-2">Ödeme geçmişimi nasıl görüntüleyebilirim?</h3>
                            <p class="{{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} text-sm leading-relaxed">Veli panelinden "Ödemeler" menüsüne giderek tüm ödeme geçmişinizi görüntüleyebilir ve fatura indirebilirsiniz.</p>
                        </div>
                    </div>
                </section>

                {{-- Yoklama Sistemi --}}
                <section id="yoklama" class="scroll-mt-28">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl {{ $isT2 ? 'bg-emerald-100 text-emerald-600' : 'bg-indigo-100 text-indigo-600' }} flex items-center justify-center">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <h2 class="text-2xl font-bold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }}">Yoklama Sistemi</h2>
                    </div>
                    <div class="space-y-4">
                        <div class="help-step bg-white rounded-2xl p-6 shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }}">
                            <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-2">Yoklama nasıl alınır?</h3>
                            <p class="{{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} text-sm leading-relaxed">Antrenör panelinden "Yoklama" menüsüne gidin, ilgili sınıfı seçin ve öğrencilerin durumunu işaretleyin.</p>
                        </div>
                        <div class="help-step bg-white rounded-2xl p-6 shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }}">
                            <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-2">Yoklama geçmişi nasıl görüntülenir?</h3>
                            <p class="{{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} text-sm leading-relaxed">Hem antrenör hem de veli panellerinden yoklama geçmişini görüntüleyebilirsiniz.</p>
                        </div>
                    </div>
                </section>

                {{-- Destek --}}
                <section id="destek" class="scroll-mt-28">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl {{ $isT2 ? 'bg-emerald-100 text-emerald-600' : 'bg-indigo-100 text-indigo-600' }} flex items-center justify-center">
                            <i class="fas fa-life-ring"></i>
                        </div>
                        <h2 class="text-2xl font-bold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }}">Destek</h2>
                    </div>
                    <div class="space-y-4">
                        <div class="help-step bg-white rounded-2xl p-6 shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }}">
                            <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-2">Teknik destek nasıl alınır?</h3>
                            <p class="{{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} text-sm leading-relaxed">İletişim sayfasından bize ulaşabilir veya e-posta gönderebilirsiniz. Müşteri destek ekibimiz 7/24 hizmetinizdedir.</p>
                        </div>
                        <div class="help-step bg-white rounded-2xl p-6 shadow-sm border {{ $isT2 ? 'border-slate-100' : 'border-gray-100' }}">
                            <h3 class="font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-2">Şifremi unuttum, ne yapmalıyım?</h3>
                            <p class="{{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} text-sm leading-relaxed">Giriş sayfasındaki "Şifremi Unuttum" linkine tıklayarak şifre sıfırlama e-postası alabilirsiniz.</p>
                        </div>
                    </div>
                </section>

                {{-- CTA --}}
                <div class="rounded-2xl p-8 text-center {{ $isT2 ? 'bg-emerald-500/10 border border-emerald-200/60' : 'bg-indigo-500/10 border border-indigo-200/60' }}">
                    <h3 class="text-xl font-semibold {{ $isT2 ? 'text-slate-800' : 'text-gray-900' }} mb-2">Hala yardıma mı ihtiyacınız var?</h3>
                    <p class="{{ $isT2 ? 'text-slate-600' : 'text-gray-600' }} mb-6">Sorunuzun cevabını bulamadıysanız, bizimle iletişime geçmekten çekinmeyin.</p>
                    <a href="{{ route('contact') }}" class="inline-flex items-center px-6 py-3 {{ $isT2 ? 'bg-emerald-500 hover:bg-emerald-600 text-white' : 'bg-indigo-600 hover:bg-indigo-700 text-white' }} rounded-xl font-medium transition-colors shadow-md">
                        <i class="fas fa-envelope mr-2"></i>İletişime Geç
                    </a>
                </div>
            </main>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.help-nav').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const id = this.getAttribute('href').slice(1);
        document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        document.querySelectorAll('.help-nav').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>
@endpush
@endsection
