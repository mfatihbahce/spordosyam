@extends('layouts.panel')

@section('title', 'Genel Ayarlar')
@section('page-title', 'Genel Ayarlar')
@section('page-description', 'Sistem genel ayarlarını yönetin')

@section('sidebar-menu')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
@push('styles')
<style>
    .settings-input:focus { outline: none; }
    .settings-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.06); }
    .settings-related-link:hover { background: rgba(99, 102, 241, 0.06); }
</style>
@endpush

<div class="w-full max-w-none pb-24">
    <form id="settings-form" action="{{ route('superadmin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 xl:gap-8">
            {{-- Sol sütun --}}
            <div class="space-y-6 xl:space-y-8">
                {{-- 1. Uygulama Bilgileri --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-shadow settings-card">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-globe text-indigo-600"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-semibold text-slate-800">Uygulama Bilgileri</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Site adı ve adresi; e-postalar ve bildirimlerde kullanılır.</p>
                        </div>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                Uygulama Adı <span class="text-red-500" title="Zorunlu alan">*</span>
                            </label>
                            <input type="text" name="app_name" value="{{ old('app_name', $settings['app_name']) }}"
                                   class="settings-input w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow"
                                   placeholder="Örn: Spordosyam" required>
                            @error('app_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                Uygulama URL <span class="text-red-500" title="Zorunlu alan">*</span>
                            </label>
                            <input type="url" name="app_url" value="{{ old('app_url', $settings['app_url']) }}"
                                   class="settings-input w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow"
                                   placeholder="https://panel.spordosyam.com" required>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1"><i class="fas fa-info-circle text-slate-400"></i> Sonunda / olmadan girin.</p>
                            @error('app_url')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Kısa Açıklama <span class="text-slate-400 font-normal">(isteğe bağlı)</span></label>
                            <input type="text" name="app_tagline" value="{{ old('app_tagline', $settings['app_tagline'] ?? '') }}"
                                   class="settings-input w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow"
                                   placeholder="Örn: Spor okulu yönetim paneli">
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1"><i class="fas fa-info-circle text-slate-400"></i> Footer veya e-posta alt metninde kullanılabilir.</p>
                            @error('app_tagline')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- 2. Dil ve Zaman Dilimi --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-shadow settings-card">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-language text-indigo-600"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-semibold text-slate-800">Dil ve Zaman Dilimi</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Varsayılan dil ve tüm tarih/saat gösterimleri.</p>
                        </div>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Varsayılan Dil <span class="text-red-500">*</span></label>
                            <select name="app_locale" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow" required>
                                <option value="tr" {{ ($settings['app_locale'] ?? 'tr') == 'tr' ? 'selected' : '' }}>Türkçe</option>
                                <option value="en" {{ ($settings['app_locale'] ?? '') == 'en' ? 'selected' : '' }}>English</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Zaman Dilimi <span class="text-red-500">*</span></label>
                            <select name="app_timezone" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow" required>
                                <option value="Europe/Istanbul" {{ ($settings['app_timezone'] ?? '') == 'Europe/Istanbul' ? 'selected' : '' }}>Europe/Istanbul (GMT+3)</option>
                                <option value="UTC" {{ ($settings['app_timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC (GMT+0)</option>
                                <option value="Europe/London" {{ ($settings['app_timezone'] ?? '') == 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT+0/+1)</option>
                                <option value="Europe/Berlin" {{ ($settings['app_timezone'] ?? '') == 'Europe/Berlin' ? 'selected' : '' }}>Europe/Berlin (GMT+1)</option>
                                <option value="Asia/Dubai" {{ ($settings['app_timezone'] ?? '') == 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (GMT+4)</option>
                                <option value="America/New_York" {{ ($settings['app_timezone'] ?? '') == 'America/New_York' ? 'selected' : '' }}>America/New_York (GMT-5)</option>
                                <option value="America/Los_Angeles" {{ ($settings['app_timezone'] ?? '') == 'America/Los_Angeles' ? 'selected' : '' }}>America/Los_Angeles (GMT-8)</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- 3. Tarih ve Saat Formatı --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-shadow settings-card">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar-alt text-indigo-600"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-semibold text-slate-800">Tarih ve Saat Formatı</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Panelde tarih/saat gösterim formatı (PHP date formatı).</p>
                        </div>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tarih formatı</label>
                            <input type="text" name="date_format" value="{{ old('date_format', $settings['date_format'] ?? 'd.m.Y') }}"
                                   class="settings-input w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow"
                                   placeholder="d.m.Y">
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1"><i class="fas fa-info-circle text-slate-400"></i> d.m.Y → 07.02.2026 · Y-m-d → 2026-02-07</p>
                            @error('date_format')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Saat formatı</label>
                            <input type="text" name="time_format" value="{{ old('time_format', $settings['time_format'] ?? 'H:i') }}"
                                   class="settings-input w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow"
                                   placeholder="H:i">
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1"><i class="fas fa-info-circle text-slate-400"></i> H:i → 14:30 · h:i A → 02:30 PM</p>
                            @error('time_format')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- 4. Demo Ayarları --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-shadow settings-card">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-amber-600"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-semibold text-slate-800">Demo Ayarları</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Başvuru onayında "Demo" seçildiğinde kullanılacak varsayılan süre (gün).</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Varsayılan demo süresi (gün)</label>
                        <input type="number" name="default_demo_days" value="{{ old('default_demo_days', $settings['default_demo_days'] ?? 14) }}"
                               min="1" max="365" class="w-full max-w-xs px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                        @error('default_demo_days')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Sağ sütun --}}
            <div class="space-y-6 xl:space-y-8">
                {{-- Panel Tasarımı --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-shadow settings-card">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-palette text-indigo-600"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-semibold text-slate-800">Panel Tasarımı</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Tüm panel sayfalarında (admin, antrenör, veli, superadmin) kullanılacak arayüz teması.</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Aktif Tasarım <span class="text-red-500">*</span></label>
                        <select name="active_design" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                            <option value="design_1" {{ old('active_design', $settings['active_design'] ?? 'design_1') == 'design_1' ? 'selected' : '' }}>Tasarım 1 — Açık tema (mevcut)</option>
                            <option value="design_2" {{ old('active_design', $settings['active_design'] ?? 'design_1') == 'design_2' ? 'selected' : '' }}>Tasarım 2 — Koyu tema (minimal)</option>
                        </select>
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1"><i class="fas fa-info-circle text-slate-400"></i> Değişiklik tüm giriş yapmış kullanıcılara anında yansır.</p>
                        @error('active_design')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Anasayfa Tema --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-shadow settings-card">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-home text-indigo-600"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-semibold text-slate-800">Anasayfa Tema</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Site ana sayfası (landing) görünümü. Ziyaretçiler giriş yapmadan önce bu sayfayı görür.</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Anasayfa Tema <span class="text-red-500">*</span></label>
                        <select name="homepage_theme" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                            <option value="theme_1" {{ old('homepage_theme', $settings['homepage_theme'] ?? 'theme_1') == 'theme_1' ? 'selected' : '' }}>Anasayfa Tema 1 — Açık (mevcut)</option>
                            <option value="theme_2" {{ old('homepage_theme', $settings['homepage_theme'] ?? 'theme_1') == 'theme_2' ? 'selected' : '' }}>Anasayfa Tema 2 — Koyu (minimal)</option>
                        </select>
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1"><i class="fas fa-info-circle text-slate-400"></i> Değişiklik anasayfaya giren tüm ziyaretçilere yansır.</p>
                        @error('homepage_theme')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Geliştirici Ayarları --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-shadow settings-card">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-code text-slate-600"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-semibold text-slate-800">Geliştirici Ayarları</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Sadece geliştirme ortamında açık olmalı; canlıda kapalı tutun.</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="hidden" name="app_debug" value="0">
                            <input type="checkbox" name="app_debug" value="1" {{ old('app_debug', $settings['app_debug'] ?? false) ? 'checked' : '' }}
                                   class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 size-4">
                            <div>
                                <span class="text-sm font-medium text-slate-700 group-hover:text-slate-800">Debug Modu</span>
                                <p class="text-xs text-slate-500 mt-0.5">Açıkken detaylı hata mesajları ve stack trace gösterilir. Canlıda güvenlik riski oluşturabilir.</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Sistem Bilgisi --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-shadow settings-card">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-server text-slate-600"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-semibold text-slate-800">Sistem Bilgisi</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Ortam ve sürüm bilgileri (salt okunur).</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-0 text-sm">
                            <div class="flex justify-between gap-4 py-3 border-b border-slate-100 first:pt-0">
                                <dt class="text-slate-500">Ortam</dt>
                                <dd class="font-medium text-slate-800">{{ $settings['app_env'] ?? config('app.env') }}</dd>
                            </div>
                            <div class="flex justify-between gap-4 py-3 border-b border-slate-100">
                                <dt class="text-slate-500">Laravel</dt>
                                <dd class="font-medium text-slate-800">{{ $settings['laravel_version'] ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between gap-4 py-3 border-b border-slate-100">
                                <dt class="text-slate-500">PHP</dt>
                                <dd class="font-medium text-slate-800">{{ $settings['php_version'] ?? PHP_VERSION }}</dd>
                            </div>
                            <div class="flex justify-between gap-4 py-3 border-b border-slate-100">
                                <dt class="text-slate-500">Önbellek</dt>
                                <dd class="font-medium text-slate-800">{{ $settings['cache_driver'] ?? config('cache.default') }}</dd>
                            </div>
                            <div class="flex justify-between gap-4 py-3">
                                <dt class="text-slate-500">Oturum</dt>
                                <dd class="font-medium text-slate-800">{{ $settings['session_driver'] ?? config('session.driver') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- İlgili ayarlar (açıklamalı kartlar) --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-link text-indigo-600"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-semibold text-slate-800">İlgili ayarlar</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Diğer ayar sayfalarına hızlı erişim.</p>
                        </div>
                    </div>
                    <div class="p-4 space-y-1">
                        <a href="{{ route('superadmin.payment-settings.index') }}" class="settings-related-link flex items-center gap-3 px-4 py-3 rounded-xl text-left transition-colors">
                            <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-credit-card text-green-600"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-sm font-medium text-slate-800 block">Ödeme Ayarları</span>
                                <span class="text-xs text-slate-500">İyzipay / ödeme altyapısı ve komisyon oranları.</span>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300 text-sm"></i>
                        </a>
                        <a href="{{ route('superadmin.footer-settings.index') }}" class="settings-related-link flex items-center gap-3 px-4 py-3 rounded-xl text-left transition-colors">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-align-left text-blue-600"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-sm font-medium text-slate-800 block">Footer Ayarları</span>
                                <span class="text-xs text-slate-500">Alt bilgi, sosyal medya ve iletişim metinleri.</span>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300 text-sm"></i>
                        </a>
                        @if(\Illuminate\Support\Facades\Route::has('superadmin.security.index'))
                        <a href="{{ route('superadmin.security.index') }}" class="settings-related-link flex items-center gap-3 px-4 py-3 rounded-xl text-left transition-colors">
                            <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-shield-alt text-amber-600"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-sm font-medium text-slate-800 block">Güvenlik Ayarları</span>
                                <span class="text-xs text-slate-500">Şifre kuralları, oturum süresi ve 2FA.</span>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300 text-sm"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Sticky Kaydet çubuğu --}}
    <div class="fixed bottom-0 left-0 right-0 xl:left-64 bg-white border-t border-slate-200 shadow-lg z-30 px-4 py-4">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-slate-500 flex items-center gap-2">
                <i class="fas fa-info-circle text-indigo-400"></i>
                Değişikliklerin tam etkili olması için uygulamayı veya önbelleği yenilemeniz gerekebilir.
            </p>
            <button type="submit" form="settings-form" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all shadow-md hover:shadow-lg">
                <i class="fas fa-save mr-2"></i>Kaydet
            </button>
        </div>
    </div>
</div>
@endsection
