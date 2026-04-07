@extends('layouts.panel')

@section('title', 'Grafikler')
@section('page-title', 'Grafikler ve Analitik')
@section('page-description', 'Ödeme trendleri, okul bazında gelir ve komisyon analizi')

@section('sidebar-menu')
@include('superadmin.partials.sidebar')
@endsection

@section('content')
@php
    $maxMonthly = $monthlyPayments->isEmpty() ? 1 : $monthlyPayments->max('total');
    $maxDaily = $dailyPayments->isEmpty() ? 1 : $dailyPayments->max('total');
    $maxSchool = $schoolPayments->isEmpty() ? 1 : $schoolPayments->max('total');
@endphp

{{-- KPI kartları --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center text-green-600">
                <i class="fas fa-lira-sign text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Toplam Gelir</p>
                <p class="text-lg font-bold text-green-700 truncate" title="{{ number_format($stats['total_revenue'], 2, ',', '.') }} ₺">{{ number_format($stats['total_revenue'], 2, ',', '.') }} ₺</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                <i class="fas fa-calendar-alt text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Bu Ay Gelir</p>
                <p class="text-lg font-bold text-indigo-700 truncate" title="{{ number_format($stats['monthly_revenue'], 2, ',', '.') }} ₺">{{ number_format($stats['monthly_revenue'], 2, ',', '.') }} ₺</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                <i class="fas fa-credit-card text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Toplam İşlem</p>
                <p class="text-lg font-bold text-slate-800">{{ number_format($stats['total_transactions']) }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600">
                <i class="fas fa-chart-line text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Ort. İşlem</p>
                <p class="text-lg font-bold text-violet-700">{{ number_format($stats['average_transaction'], 2, ',', '.') }} ₺</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                <i class="fas fa-percent text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Toplam Komisyon</p>
                <p class="text-lg font-bold text-emerald-700">{{ number_format($stats['total_commission'], 2, ',', '.') }} ₺</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600">
                <i class="fas fa-share-alt text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Dağıtılan</p>
                <p class="text-lg font-bold text-sky-700">{{ number_format($stats['total_distributed'], 2, ',', '.') }} ₺</p>
            </div>
        </div>
    </div>
</div>

{{-- Son 12 ay ödeme trendi (bar) --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm mb-8">
    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
        <h3 class="text-base font-semibold text-slate-800">Son 12 ay ödeme trendi</h3>
        <p class="text-xs text-slate-500 mt-0.5">Aylık toplam ödeme tutarı ve işlem sayısı</p>
    </div>
    <div class="p-6">
        @if($monthlyPayments->count() > 0)
        <div class="space-y-4">
            @foreach($monthlyPayments as $p)
            @php
                $monthName = \Carbon\Carbon::createFromDate($p->year, $p->month, 1)->locale('tr')->translatedFormat('F Y');
                $pct = $maxMonthly > 0 ? min(100, round(((float) $p->total / $maxMonthly) * 100)) : 0;
            @endphp
            <div class="flex flex-wrap items-center gap-4">
                <div class="w-28 flex-shrink-0 text-sm font-medium text-slate-700">{{ $monthName }}</div>
                <div class="flex-1 min-w-0">
                    <div class="h-9 bg-slate-100 rounded-xl overflow-hidden flex">
                        <div class="h-full bg-gradient-to-r from-indigo-500 to-indigo-400 rounded-xl flex items-center justify-end pr-2 transition-all" style="width: {{ max(8, $pct) }}%">
                            @if($pct >= 15)
                            <span class="text-xs font-semibold text-white">{{ number_format((float) $p->total, 0, ',', '.') }} ₺</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="w-24 text-right text-sm font-semibold text-slate-800">{{ number_format((float) $p->total, 2, ',', '.') }} ₺</div>
                <div class="w-16 text-right text-xs text-slate-500">{{ $p->count }} işlem</div>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-12 text-center text-slate-500 text-sm">Henüz aylık ödeme verisi yok.</div>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
    {{-- Son 30 gün ödeme trendi --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h3 class="text-base font-semibold text-slate-800">Son 30 gün ödeme trendi</h3>
            <p class="text-xs text-slate-500 mt-0.5">Günlük toplam tutar</p>
        </div>
        <div class="p-6 max-h-96 overflow-y-auto">
            @if($dailyPayments->count() > 0)
            <div class="space-y-2">
                @foreach($dailyPayments->take(30) as $d)
                @php $pct = $maxDaily > 0 ? min(100, round(((float) $d->total / $maxDaily) * 100)) : 0; @endphp
                <div class="flex items-center gap-3">
                    <div class="w-24 flex-shrink-0 text-xs font-medium text-slate-600">{{ \Carbon\Carbon::parse($d->date)->format('d.m.Y') }}</div>
                    <div class="flex-1 min-w-0 h-7 bg-slate-100 rounded-lg overflow-hidden flex">
                        <div class="h-full bg-green-500 rounded-lg" style="width: {{ max(5, $pct) }}%"></div>
                    </div>
                    <div class="w-20 text-right text-xs font-semibold text-slate-800">{{ number_format((float) $d->total, 0, ',', '.') }} ₺</div>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-8 text-center text-slate-500 text-sm">Henüz günlük ödeme verisi yok.</div>
            @endif
        </div>
    </div>

    {{-- Okul bazında ödeme dağılımı --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h3 class="text-base font-semibold text-slate-800">Okul bazında ödeme dağılımı</h3>
            <p class="text-xs text-slate-500 mt-0.5">Toplam ödeme tutarına göre sıralı</p>
        </div>
        <div class="p-6 max-h-96 overflow-y-auto">
            @if($schoolPayments->count() > 0)
            <div class="space-y-3">
                @foreach($schoolPayments as $sp)
                @php $pct = $maxSchool > 0 ? min(100, round(((float) $sp->total / $maxSchool) * 100)) : 0; @endphp
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('superadmin.schools.show', $sp->school_id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 truncate block">{{ $sp->school->name ?? 'Bilinmeyen Okul' }}</a>
                        <p class="text-xs text-slate-500">{{ $sp->count }} işlem</p>
                    </div>
                    <div class="w-32 flex-shrink-0">
                        <div class="h-7 bg-slate-100 rounded-lg overflow-hidden flex">
                            <div class="h-full bg-emerald-500 rounded-lg flex items-center justify-end pr-1.5" style="width: {{ max(15, $pct) }}%">
                                @if($pct >= 25)
                                <span class="text-xs font-semibold text-white">{{ number_format((float) $sp->total / 1000, 1, ',', '.') }}k</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="w-24 text-right text-sm font-semibold text-slate-800">{{ number_format((float) $sp->total, 2, ',', '.') }} ₺</div>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-8 text-center text-slate-500 text-sm">Henüz okul bazında ödeme verisi yok.</div>
            @endif
        </div>
    </div>
</div>

{{-- Özet tablo: Okul bazında detay --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
        <h3 class="text-base font-semibold text-slate-800">Okul bazında özet tablo</h3>
        <p class="text-xs text-slate-500 mt-0.5">Tutar ve işlem sayısı · Okul detayına gidebilirsiniz</p>
    </div>
    <div class="overflow-x-auto">
        @if($schoolPayments->count() > 0)
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Okul</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">Toplam Tutar</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">İşlem sayısı</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($schoolPayments as $sp)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-4 py-3">
                        <a href="{{ route('superadmin.schools.show', $sp->school_id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">{{ $sp->school->name ?? '—' }}</a>
                    </td>
                    <td class="px-4 py-3 text-sm text-right font-semibold text-green-700">{{ number_format((float) $sp->total, 2, ',', '.') }} ₺</td>
                    <td class="px-4 py-3 text-sm text-right text-slate-600">{{ $sp->count }} adet</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('superadmin.schools.show', $sp->school_id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Detay</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-8 text-center text-slate-500 text-sm">Henüz veri yok.</div>
        @endif
    </div>
</div>
@endsection
