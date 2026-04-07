@extends('layouts.panel')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Sistem genel bakış, istatistikler ve son hareketler')

@section('sidebar-menu')
@include('superadmin.partials.sidebar')
@endsection

@section('content')
@php
    $maxMonthly = $monthly_stats->isEmpty() ? 1 : $monthly_stats->max('total');
@endphp

{{-- Hızlı işlemler (en üst) --}}
<div class="mb-8">
    <h3 class="text-sm font-semibold text-slate-700 mb-3">Hızlı işlemler</h3>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('superadmin.applications.index') }}" class="inline-flex items-center px-4 py-2.5 bg-amber-100 text-amber-800 text-sm font-medium rounded-xl hover:bg-amber-200 transition-colors">
            <i class="fas fa-file-alt mr-2"></i>Bekleyen Başvurular
        </a>
        <a href="{{ route('superadmin.schools.index') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-xl hover:bg-indigo-200 transition-colors">
            <i class="fas fa-school mr-2"></i>Okullar
        </a>
        <a href="{{ route('superadmin.schools.expired') }}" class="inline-flex items-center px-4 py-2.5 bg-red-100 text-red-800 text-sm font-medium rounded-xl hover:bg-red-200 transition-colors">
            <i class="fas fa-clock mr-2"></i>Lisansı Biten Okullar
        </a>
        <a href="{{ route('superadmin.payments.index') }}" class="inline-flex items-center px-4 py-2.5 bg-green-100 text-green-800 text-sm font-medium rounded-xl hover:bg-green-200 transition-colors">
            <i class="fas fa-credit-card mr-2"></i>Ödemeler
        </a>
        <a href="{{ route('superadmin.reports.index') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-100 text-slate-800 text-sm font-medium rounded-xl hover:bg-slate-200 transition-colors">
            <i class="fas fa-clipboard-list mr-2"></i>Genel Raporlar
        </a>
        <a href="{{ route('superadmin.analytics.index') }}" class="inline-flex items-center px-4 py-2.5 bg-violet-100 text-violet-800 text-sm font-medium rounded-xl hover:bg-violet-200 transition-colors">
            <i class="fas fa-chart-bar mr-2"></i>Grafikler
        </a>
    </div>
</div>

{{-- Özet KPI kartları: 1. satır 4 kart, 2. satır 3 kart --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                <i class="fas fa-school text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Toplam Okul</p>
                <p class="text-xl font-bold text-slate-800">{{ $stats['total_schools'] }}</p>
                <p class="text-xs text-slate-500">{{ $stats['active_license_schools'] }} aktif lisans · {{ $stats['expired_license_schools'] }} süresi dolmuş</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                <i class="fas fa-file-alt text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Bekleyen Başvuru</p>
                <p class="text-xl font-bold text-amber-700">{{ $stats['pending_applications'] }}</p>
                <p class="text-xs text-slate-500">{{ $stats['applications_approved'] }} onaylı toplam</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center text-green-600">
                <i class="fas fa-lira-sign text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Toplam Ödeme</p>
                <p class="text-lg font-bold text-green-700 truncate" title="{{ number_format($stats['total_payments'], 2, ',', '.') }} ₺">{{ number_format($stats['total_payments'], 2, ',', '.') }} ₺</p>
                <p class="text-xs text-slate-500">{{ $stats['payment_count'] }} işlem</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                <i class="fas fa-calendar-alt text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Bu Ay Ödeme</p>
                <p class="text-lg font-bold text-indigo-700 truncate" title="{{ number_format($stats['monthly_payments'], 2, ',', '.') }} ₺">{{ number_format($stats['monthly_payments'], 2, ',', '.') }} ₺</p>
                <p class="text-xs text-slate-500">{{ $stats['monthly_payment_count'] }} işlem</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                <i class="fas fa-share-alt text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Dağıtım / Komisyon</p>
                <p class="text-sm font-bold text-emerald-700">{{ number_format($stats['total_distributions'], 0, ',', '.') }} ₺</p>
                <p class="text-xs text-slate-500">Komisyon: {{ number_format($stats['total_commission'], 2, ',', '.') }} ₺</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                <i class="fas fa-users text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Kullanıcılar</p>
                <p class="text-xl font-bold text-slate-800">{{ $stats['total_users'] }}</p>
                <p class="text-xs text-slate-500">Yön: {{ $stats['users_admin'] }} · Ant: {{ $stats['users_coach'] }} · Veli: {{ $stats['users_parent'] }}</p>
            </div>
        </div>
    </div>
    {{-- 7. kart: Lisans uzatım geliri (2. satırda 3 karttan biri) --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600">
                <i class="fas fa-calendar-plus text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Lisans Uzatım Geliri</p>
                <p class="text-lg font-bold text-violet-700">{{ number_format($stats['total_extension_revenue'] ?? 0, 2, ',', '.') }} ₺</p>
                <p class="text-xs text-slate-500">Superadmin geliri</p>
            </div>
        </div>
    </div>
</div>

{{-- Son 12 ay ödeme trendi --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm mb-8">
    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
        <h3 class="text-base font-semibold text-slate-800">Son 12 ay ödeme trendi</h3>
        <p class="text-xs text-slate-500 mt-0.5">Aylık toplam ödeme tutarı</p>
    </div>
    <div class="p-6">
        @if($monthly_stats->count() > 0)
        <div class="space-y-3">
            @foreach($monthly_stats as $m)
            @php
                $monthName = \Carbon\Carbon::createFromDate($m->year ?? now()->year, $m->month, 1)->locale('tr')->translatedFormat('F Y');
                $pct = $maxMonthly > 0 ? min(100, round(((float) $m->total / $maxMonthly) * 100)) : 0;
            @endphp
            <div class="flex items-center gap-4">
                <div class="w-28 flex-shrink-0 text-sm font-medium text-slate-700">{{ $monthName }}</div>
                <div class="flex-1 min-w-0 h-8 bg-slate-100 rounded-xl overflow-hidden flex">
                    <div class="h-full bg-gradient-to-r from-indigo-500 to-indigo-400 rounded-xl flex items-center justify-end pr-2 transition-all" style="width: {{ max(5, $pct) }}%">
                        @if($pct >= 20)
                        <span class="text-xs font-semibold text-white">{{ number_format((float) $m->total, 0, ',', '.') }} ₺</span>
                        @endif
                    </div>
                </div>
                <div class="w-24 text-right text-sm font-semibold text-slate-800">{{ number_format((float) $m->total, 2, ',', '.') }} ₺</div>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-8 text-center text-slate-500 text-sm">Henüz aylık ödeme verisi yok.</div>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
    {{-- Son başvurular --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-800">Son bekleyen başvurular</h3>
                <p class="text-xs text-slate-500 mt-0.5">En son 5 talep</p>
            </div>
            <a href="{{ route('superadmin.applications.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tümü →</a>
        </div>
        <div class="overflow-x-auto">
            @if($recent_applications->count() > 0)
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Okul Adı</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">İletişim</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Tarih</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recent_applications as $app)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $app->school_name }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $app->email }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $app->created_at->format('d.m.Y H:i') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('superadmin.applications.show', $app) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Detay</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-8 text-center text-slate-500 text-sm">Bekleyen başvuru yok.</div>
            @endif
        </div>
    </div>

    {{-- Son eklenen okullar --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-800">Son eklenen okullar</h3>
                <p class="text-xs text-slate-500 mt-0.5">En son 5 okul</p>
            </div>
            <a href="{{ route('superadmin.schools.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tümü →</a>
        </div>
        <div class="p-4">
            @if($recent_schools->count() > 0)
            <ul class="space-y-3">
                @foreach($recent_schools as $school)
                @php
                    $licenseLabel = match($school->license_type) { 'demo' => 'Demo', 'free' => 'Ücretsiz', 'paid' => 'Ücretli', default => '—' };
                    $expired = $school->demo_expires_at && $school->demo_expires_at->endOfDay()->isPast();
                @endphp
                <li class="flex items-center justify-between gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                    <div class="min-w-0">
                        <a href="{{ route('superadmin.schools.show', $school) }}" class="font-medium text-slate-800 hover:text-indigo-600 truncate block">{{ $school->name }}</a>
                        <p class="text-xs text-slate-500 truncate">{{ $school->email }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $expired ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">{{ $licenseLabel }}</span>
                        <span class="text-xs text-slate-500">{{ $school->created_at->format('d.m.Y') }}</span>
                        <a href="{{ route('superadmin.schools.show', $school) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Detay</a>
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <div class="p-8 text-center text-slate-500 text-sm">Henüz okul yok.</div>
            @endif
        </div>
    </div>
</div>

{{-- Son ödemeler --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-base font-semibold text-slate-800">Son tamamlanan ödemeler</h3>
            <p class="text-xs text-slate-500 mt-0.5">En son 8 işlem</p>
        </div>
        <a href="{{ route('superadmin.payments.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tüm ödemeler →</a>
    </div>
    <div class="overflow-x-auto">
        @if($recent_payments->count() > 0)
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Okul</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Öğrenci</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">Tutar</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Tarih</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($recent_payments as $payment)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $payment->school->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">
                        @if($payment->studentFee && $payment->studentFee->student)
                            {{ $payment->studentFee->student->first_name }} {{ $payment->studentFee->student->last_name }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-right font-semibold text-green-700">{{ number_format($payment->amount, 2, ',', '.') }} ₺</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('superadmin.payments.show', $payment) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Detay</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-8 text-center text-slate-500 text-sm">Henüz tamamlanan ödeme yok.</div>
        @endif
    </div>
</div>
@endsection
