@extends('layouts.panel')

@section('title', 'Genel Raporlar')
@section('page-title', 'Genel Raporlar')
@section('page-description', 'Okullar, ödemeler, dağıtımlar ve başvurular özeti')

@section('sidebar-menu')
@include('superadmin.partials.sidebar')
@endsection

@section('content')
{{-- Özet KPI kartları --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                <i class="fas fa-school text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Toplam Okul</p>
                <p class="text-xl font-bold text-slate-800">{{ $stats['total_schools'] }}</p>
                <p class="text-xs text-slate-500">{{ $stats['active_license_schools'] }} aktif lisans · {{ $stats['expired_license_schools'] }} süresi dolmuş</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center text-green-600">
                <i class="fas fa-lira-sign text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Toplam Ödeme</p>
                <p class="text-xl font-bold text-green-700">{{ number_format($stats['total_payment_amount'], 2, ',', '.') }} ₺</p>
                <p class="text-xs text-slate-500">{{ $stats['total_payment_count'] }} işlem</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                <i class="fas fa-share-alt text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Dağıtım (Okullara)</p>
                <p class="text-xl font-bold text-emerald-700">{{ number_format($stats['total_distribution_amount'], 2, ',', '.') }} ₺</p>
                <p class="text-xs text-slate-500">Komisyon: {{ number_format($stats['total_commission_earned'], 2, ',', '.') }} ₺</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                <i class="fas fa-users text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Kullanıcılar</p>
                <p class="text-xl font-bold text-slate-800">{{ $stats['total_users'] }}</p>
                <p class="text-xs text-slate-500">Yön: {{ $stats['users_admin'] }} · Ant: {{ $stats['users_coach'] }} · Veli: {{ $stats['users_parent'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600">
                <i class="fas fa-file-alt text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Başvurular</p>
                <p class="text-lg font-bold text-slate-800">{{ $stats['applications_pending'] }} bekleyen</p>
                <p class="text-xs text-slate-500">{{ $stats['applications_approved'] }} onaylı · {{ $stats['applications_rejected'] }} reddedilen</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                <i class="fas fa-calendar-alt text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Bu Ay Ödeme</p>
                <p class="text-lg font-bold text-indigo-700">{{ number_format($stats['monthly_payment_amount'], 2, ',', '.') }} ₺</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600">
                <i class="fas fa-calendar-plus text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Lisans Uzatım Geliri</p>
                <p class="text-lg font-bold text-violet-700">{{ number_format($stats['total_extension_revenue'], 2, ',', '.') }} ₺</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
    {{-- Son ödemeler --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h3 class="text-base font-semibold text-slate-800">Son tamamlanan ödemeler</h3>
            <p class="text-xs text-slate-500 mt-0.5">En son 10 işlem</p>
        </div>
        <div class="overflow-x-auto">
            @if($recentPayments->count() > 0)
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Okul</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">Tutar</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recentPayments as $p)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $p->school->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-green-700">{{ number_format($p->amount, 2, ',', '.') }} ₺</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $p->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-8 text-center text-slate-500 text-sm">Henüz tamamlanan ödeme yok.</div>
            @endif
        </div>
        @if($recentPayments->count() > 0)
        <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50">
            <a href="{{ route('superadmin.payments.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tüm ödemeler →</a>
        </div>
        @endif
    </div>

    {{-- Son dağıtımlar --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h3 class="text-base font-semibold text-slate-800">Son tamamlanan dağıtımlar</h3>
            <p class="text-xs text-slate-500 mt-0.5">En son 10 işlem</p>
        </div>
        <div class="overflow-x-auto">
            @if($recentDistributions->count() > 0)
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Okul</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">Net</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recentDistributions as $d)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $d->school->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-emerald-700">{{ number_format($d->net_amount, 2, ',', '.') }} ₺</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $d->processed_at ? $d->processed_at->format('d.m.Y H:i') : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-8 text-center text-slate-500 text-sm">Henüz tamamlanan dağıtım yok.</div>
            @endif
        </div>
        @if($recentDistributions->count() > 0)
        <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50">
            <a href="{{ route('superadmin.distributions.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tüm dağıtımlar →</a>
        </div>
        @endif
    </div>
</div>

{{-- Okul özeti --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
        <h3 class="text-base font-semibold text-slate-800">Okul özeti</h3>
        <p class="text-xs text-slate-500 mt-0.5">Tüm okullar · öğrenci/sınıf sayısı ve lisans durumu</p>
    </div>
    <div class="overflow-x-auto">
        @if($schoolSummary->count() > 0)
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Okul</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Lisans</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-slate-500 uppercase">Öğrenci</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-slate-500 uppercase">Sınıf</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Bitiş</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($schoolSummary as $s)
                @php
                    $licenseLabel = match($s->license_type) { 'demo' => 'Demo', 'free' => 'Ücretsiz', 'paid' => 'Ücretli', default => '—' };
                    $expired = $s->demo_expires_at && $s->demo_expires_at->endOfDay()->isPast();
                @endphp
                <tr class="hover:bg-slate-50/50">
                    <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $s->name }}</td>
                    <td class="px-4 py-3 text-sm">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $expired ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">{{ $licenseLabel }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-center text-slate-600">{{ $s->students_count }}</td>
                    <td class="px-4 py-3 text-sm text-center text-slate-600">{{ $s->classes_count }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $s->demo_expires_at ? $s->demo_expires_at->format('d.m.Y') : '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('superadmin.schools.show', $s) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Detay</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-8 text-center text-slate-500 text-sm">Henüz okul yok.</div>
        @endif
    </div>
</div>
@endsection
