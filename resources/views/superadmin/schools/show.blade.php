@extends('layouts.panel')

@section('title', 'Okul Detayı')
@section('page-title', $school->name)
@section('page-description', 'Okul bilgileri ve lisans yönetimi')

@section('sidebar-menu')
@include('superadmin.partials.sidebar')
@endsection

@section('content')
@php
    $daysLeft = $school->getDaysUntilLicenseExpires();
    $licenseTypeLabels = ['demo' => 'Demo', 'free' => 'Ücretsiz', 'paid' => 'Ücretli'];
    $extensions = $school->licenseExtensions ?? collect();
    $totalExtRevenue = $school->total_extension_revenue;
    $maxAmount = $extensions->max('amount') ?: 1;
    $statusConfig = $school->isLicenseExpired()
        ? ['label' => 'Lisans süresi dolmuş', 'bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-times-circle']
        : ($daysLeft !== null && $daysLeft <= 10
            ? ['label' => $daysLeft . ' gün kaldı', 'bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'icon' => 'fa-exclamation-triangle']
            : ['label' => 'Aktif lisans', 'bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-circle']);
@endphp

{{-- Başarı mesajı --}}
@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-2xl text-sm flex items-center shadow-sm">
        <i class="fas fa-check-circle mr-3 text-green-600 text-lg"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- Üst alan: Okul adı, durum, aksiyonlar --}}
<div class="mb-8 rounded-2xl bg-gradient-to-br from-slate-50 to-indigo-50/30 border border-slate-200/80 p-6 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-start gap-4">
            <a href="{{ $school->isLicenseExpired() ? route('superadmin.schools.expired') : route('superadmin.schools.index') }}" class="mt-1 p-2 rounded-xl text-slate-500 hover:bg-white hover:text-indigo-600 transition-colors" title="Geri dön">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $school->name }}</h1>
                <p class="text-sm text-slate-500 mt-0.5">Okul bilgileri ve lisans yönetimi</p>
                <span class="inline-flex items-center mt-3 px-3 py-1.5 rounded-full text-sm font-medium {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                    <i class="fas {{ $statusConfig['icon'] }} mr-2"></i>{{ $statusConfig['label'] }}
                </span>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:flex-shrink-0">
            <button type="button" onclick="document.getElementById('extendLicenseModal').classList.remove('hidden')" class="inline-flex items-center px-5 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 shadow-md hover:shadow-lg transition-all">
                <i class="fas fa-calendar-plus mr-2"></i>Lisans Uzat
            </button>
            <a href="{{ route('superadmin.schools.edit', $school) }}" class="inline-flex items-center px-5 py-2.5 bg-white border-2 border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:border-indigo-300 hover:bg-indigo-50/50 transition-all">
                <i class="fas fa-pen mr-2"></i>Düzenle
            </a>
        </div>
    </div>
</div>

{{-- Özet kartları (KPI) --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                <i class="fas fa-calendar-alt text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Lisans bitişi</p>
                <p class="text-lg font-bold text-slate-800">{{ $school->demo_expires_at ? $school->demo_expires_at->format('d.m.Y') : '—' }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl {{ $school->isLicenseExpired() ? 'bg-red-100 text-red-600' : ($daysLeft !== null && $daysLeft <= 10 ? 'bg-amber-100 text-amber-600' : 'bg-green-100 text-green-600') }} flex items-center justify-center">
                <i class="fas fa-clock text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Kalan süre</p>
                <p class="text-lg font-bold {{ $school->isLicenseExpired() ? 'text-red-700' : ($daysLeft !== null && $daysLeft <= 10 ? 'text-amber-700' : 'text-green-700') }}">
                    @if($daysLeft === null)—
                    @elseif($daysLeft < 0)Süre doldu
                    @else{{ $daysLeft }} gün
                    @endif
                </p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600">
                <i class="fas fa-tag text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Lisans türü</p>
                <p class="text-base font-semibold text-slate-800">{{ $licenseTypeLabels[$school->license_type ?? ''] ?? ($school->license_type ?? '—') }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                <i class="fas fa-lira-sign text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Uzatım geliri</p>
                <p class="text-lg font-bold text-emerald-700">{{ number_format($totalExtRevenue, 2, ',', '.') }} ₺</p>
            </div>
        </div>
    </div>
</div>

{{-- Lisans dönemi çubuğu --}}
@if($school->created_at && $school->demo_expires_at)
@php
    $start = $school->created_at->copy()->startOfDay();
    $end = $school->demo_expires_at->copy()->startOfDay();
    $totalDays = max(1, $start->diffInDays($end));
    $elapsedDays = min($totalDays, max(0, $start->diffInDays(now()->startOfDay(), false)));
    $elapsedPct = min(100, round(($elapsedDays / $totalDays) * 100));
@endphp
<div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm mb-8">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-slate-700">Lisans dönemi</h3>
        <span class="text-xs text-slate-500">{{ $school->created_at->format('d.m.Y') }} → {{ $school->demo_expires_at->format('d.m.Y') }}</span>
    </div>
    <div class="h-4 bg-slate-100 rounded-full overflow-hidden flex">
        <div class="h-full bg-gradient-to-r from-green-500 to-green-400 rounded-l-full transition-all duration-500" style="width: {{ $elapsedPct }}%"></div>
        <div class="h-full bg-amber-400 rounded-r-full flex-1"></div>
    </div>
    <div class="flex justify-between mt-2 text-xs text-slate-500">
        <span>Başlangıç</span>
        <span>Bitiş</span>
    </div>
</div>
@endif

{{-- İki sütun: İletişim + Lisans özeti / Uzatım geçmişi --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    {{-- Sol: İletişim ve okul bilgileri --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-5 py-4 bg-slate-50 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-800">İletişim bilgileri</h3>
            </div>
            <div class="p-5">
                <ul class="space-y-4">
                    <li class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 text-sm"><i class="fas fa-envelope"></i></span>
                        <div class="min-w-0">
                            <p class="text-xs text-slate-500">E-posta</p>
                            <p class="text-sm font-medium text-slate-800 truncate">{{ $school->email }}</p>
                        </div>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 text-sm"><i class="fas fa-phone"></i></span>
                        <div>
                            <p class="text-xs text-slate-500">Telefon</p>
                            <p class="text-sm font-medium text-slate-800">{{ $school->phone ?? '—' }}</p>
                        </div>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 text-sm"><i class="fas fa-map-marker-alt"></i></span>
                        <div>
                            <p class="text-xs text-slate-500">Adres</p>
                            <p class="text-sm font-medium text-slate-800">{{ $school->address ?: '—' }}</p>
                        </div>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-lg {{ $school->is_active ? 'bg-green-100 text-green-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center text-sm"><i class="fas fa-circle"></i></span>
                        <div>
                            <p class="text-xs text-slate-500">Durum</p>
                            <p class="text-sm font-medium {{ $school->is_active ? 'text-green-700' : 'text-slate-600' }}">{{ $school->is_active ? 'Aktif' : 'Pasif' }}</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        {{-- İstatistikler --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-5 py-4 bg-slate-50 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-800">İstatistikler</h3>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-3">
                    @foreach(['Öğrenci' => $school->students_count, 'Antrenör' => $school->coaches_count, 'Sınıf' => $school->classes_count, 'Şube' => $school->branches_count, 'Branş' => $school->sport_branches_count] as $label => $count)
                    <div class="flex items-center gap-2 p-3 rounded-xl bg-slate-50">
                        <span class="text-2xl font-bold text-indigo-600">{{ $count }}</span>
                        <span class="text-xs font-medium text-slate-600">{{ $label }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Sağ: Lisans özeti + Uzatım geçmişi --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-5 py-4 bg-slate-50 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-800">Lisans özeti</h3>
                <p class="text-xs text-slate-500 mt-0.5">Oluşturulma, bitiş ve uzatım bilgisi</p>
            </div>
            <div class="p-5">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Oluşturulma</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-800">{{ $school->created_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Bitiş tarihi</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-800">{{ $school->demo_expires_at ? $school->demo_expires_at->format('d.m.Y') : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Uzatım sayısı</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-800">{{ (int) ($school->license_extended_count ?? 0) }} kez</dd>
                    </div>
                    @if(($school->license_type ?? '') === 'paid' && $school->paid_amount !== null)
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Anlaşma tutarı</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-800">{{ number_format((float) $school->paid_amount, 2, ',', '.') }} ₺</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Lisans uzatım geçmişi (tarih + fiyat barı) --}}
        @if($extensions->isNotEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-5 py-4 bg-indigo-50/50 border-b border-indigo-100">
                <h3 class="text-base font-semibold text-slate-800">Lisans uzatım geçmişi</h3>
                <p class="text-xs text-slate-600 mt-0.5">Tarih ve ücret · Toplam <strong>{{ number_format($totalExtRevenue, 2, ',', '.') }} ₺</strong></p>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    @foreach($extensions as $ext)
                    @php $widthPct = $maxAmount > 0 ? min(100, round(((float) ($ext->amount ?? 0) / $maxAmount) * 100)) : 0; @endphp
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="w-24 flex-shrink-0 text-sm font-medium text-slate-700">{{ $ext->extended_at->format('d.m.Y') }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="h-9 bg-slate-100 rounded-xl overflow-hidden flex">
                                <div class="h-full bg-indigo-500 rounded-xl flex items-center justify-end pr-2 transition-all" style="width: {{ max(20, $widthPct) }}%">
                                    @if(($ext->amount ?? 0) > 0)
                                    <span class="text-xs font-semibold text-white">{{ number_format((float) $ext->amount, 0, ',', '.') }} ₺</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="w-20 text-right text-sm font-semibold text-slate-800">
                            @if(($ext->amount ?? 0) > 0){{ number_format((float) $ext->amount, 2, ',', '.') }} ₺@else<span class="text-slate-400">—</span>@endif
                        </div>
                        <div class="w-14 text-right text-xs text-slate-500">+{{ $ext->days_added }} gün</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center shadow-sm">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                <i class="fas fa-history text-2xl"></i>
            </div>
            <p class="text-sm font-medium text-slate-600">Henüz lisans uzatımı yok</p>
            <p class="text-xs text-slate-500 mt-1">"Lisans Uzat" ile ilk uzatımı yapabilirsiniz.</p>
        </div>
        @endif
    </div>
</div>

{{-- Lisans Uzat Modal --}}
<div id="extendLicenseModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="document.getElementById('extendLicenseModal').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-200" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800">Lisans süresi uzat</h3>
                <button type="button" onclick="document.getElementById('extendLicenseModal').classList.add('hidden')" class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-sm text-slate-600 mb-4">{{ $school->name }}</p>
            @if($school->demo_expires_at)
                <p class="text-sm text-slate-600 mb-4">
                    Mevcut bitiş: <strong>{{ $school->demo_expires_at->format('d.m.Y') }}</strong>
                    @if($school->isLicenseExpired())<span class="text-red-600 ml-1">(süresi dolmuş)</span>
                    @else<span class="text-slate-500 ml-1">({{ $school->getDaysUntilLicenseExpires() }} gün kaldı)</span>@endif
                </p>
            @endif

            <form action="{{ route('superadmin.schools.extend-license', $school) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="modal_days" class="block text-sm font-semibold text-slate-700 mb-2">Kaç gün eklensin?</label>
                    <input type="number" name="days" id="modal_days" value="{{ old('days', 30) }}" min="1" max="365" required class="block w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="document.getElementById('modal_days').value=30" class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-200">30</button>
                    <button type="button" onclick="document.getElementById('modal_days').value=90" class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-200">90</button>
                    <button type="button" onclick="document.getElementById('modal_days').value=180" class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-200">180</button>
                    <button type="button" onclick="document.getElementById('modal_days').value=365" class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-200">365</button>
                </div>
                <div>
                    <label for="modal_amount" class="block text-sm font-semibold text-slate-700 mb-2">Ücret (₺) <span class="text-slate-400 font-normal">— Kazanç</span></label>
                    <input type="number" name="amount" id="modal_amount" value="{{ old('amount') }}" min="0" step="0.01" placeholder="Örn. 500" class="block w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 shadow-md">
                        <i class="fas fa-calendar-plus mr-2"></i>Lisansı Uzat
                    </button>
                    <button type="button" onclick="document.getElementById('extendLicenseModal').classList.add('hidden')" class="px-4 py-3 border-2 border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50">
                        İptal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
