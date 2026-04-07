@extends('layouts.panel')

@section('title', 'Raporlar')
@section('page-title', 'Raporlar')
@section('page-description', 'Okul istatistiklerini görüntüleyin')

@section('sidebar-menu')
    @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">Raporlar</h2>
    <p class="text-sm text-gray-500 mt-1">Öğrenci, ödeme, yoklama ve sınıf istatistiklerinize ait özet raporlar.</p>
</div>

{{-- KPI Kartları --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    <a href="{{ route('admin.students.index') }}" class="bg-white rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg hover:border-indigo-100 transition-all group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Toplam Öğrenci</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_students']) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['active_students'] }} aktif</p>
            </div>
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                <i class="fas fa-user-graduate text-indigo-600 text-xl"></i>
            </div>
        </div>
    </a>
    <a href="{{ route('admin.payments.index') }}" class="bg-white rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg hover:border-emerald-100 transition-all group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Toplam Ödeme</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_payments'], 2) }} ₺</p>
                <p class="text-xs text-gray-500 mt-1">Bu ay: {{ number_format($stats['monthly_payments'], 2) }} ₺</p>
            </div>
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                <i class="fas fa-lira-sign text-emerald-600 text-xl"></i>
            </div>
        </div>
    </a>
    <a href="{{ route('admin.attendances.index') }}" class="bg-white rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg hover:border-violet-100 transition-all group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Toplam Katılım</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_attendances']) }}</p>
                <p class="text-xs text-gray-500 mt-1">Katılım oranı %{{ $stats['attendance_rate'] }}</p>
            </div>
            <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center group-hover:bg-violet-200 transition-colors">
                <i class="fas fa-check-circle text-violet-600 text-xl"></i>
            </div>
        </div>
    </a>
    <a href="{{ route('admin.classes.index') }}" class="bg-white rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg hover:border-sky-100 transition-all group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Aktif Sınıf</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['active_classes'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['total_classes'] }} toplam</p>
            </div>
            <div class="w-12 h-12 bg-sky-100 rounded-xl flex items-center justify-center group-hover:bg-sky-200 transition-colors">
                <i class="fas fa-users text-sky-600 text-xl"></i>
            </div>
        </div>
    </a>
    <a href="{{ route('admin.coaches.index') }}" class="bg-white rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg hover:border-amber-100 transition-all group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Antrenör</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_coaches'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['total_parents'] }} veli</p>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                <i class="fas fa-user-tie text-amber-600 text-xl"></i>
            </div>
        </div>
    </a>
    <a href="{{ route('admin.student-fees.index') }}" class="bg-white rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg hover:border-rose-100 transition-all group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Bekleyen Aidat</p>
                <p class="text-2xl font-bold {{ $stats['pending_fees'] > 0 ? 'text-rose-600' : 'text-gray-900' }} mt-1">{{ number_format($stats['pending_fees'], 2) }} ₺</p>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['pending_fees_count'] }} adet</p>
            </div>
            <div class="w-12 h-12 bg-rose-100 rounded-xl flex items-center justify-center group-hover:bg-rose-200 transition-colors">
                <i class="fas fa-exclamation-triangle text-rose-600 text-xl"></i>
            </div>
        </div>
    </a>
</div>

{{-- Son 7 Gün Katılım --}}
@if($last7Days->sum('total') > 0)
<div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800">Son 7 Gün Katılım</h3>
        <p class="text-sm text-gray-500 mt-0.5">Günlük katılan öğrenci sayısı</p>
    </div>
    <div class="p-6">
        <div class="flex items-end gap-2 h-32">
            @php $maxVal = max(1, $last7Days->max('total')); @endphp
            @foreach($last7Days as $day)
            <div class="flex-1 flex flex-col items-center gap-1">
                <div class="w-full bg-gray-100 rounded-t-lg overflow-hidden flex flex-col justify-end" style="min-height: 80px;">
                    <div class="bg-violet-500 rounded-t transition-all" style="height: {{ $maxVal > 0 ? round(($day['total'] / $maxVal) * 70) : 0 }}px;" title="{{ $day['label'] }}: {{ $day['present'] }} katıldı, {{ $day['absent'] }} devamsız"></div>
                </div>
                <span class="text-xs font-medium text-gray-600">{{ $day['label'] }}</span>
                <span class="text-xs text-gray-400">{{ $day['present'] }}/{{ $day['total'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Sınıfa göre yoklama --}}
<div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Sınıfa Göre Yoklama</h3>
            <p class="text-sm text-gray-500 mt-0.5">Katılan / devamsız dağılımı</p>
        </div>
        <a href="{{ route('admin.attendances.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tümünü Gör</a>
    </div>
    <div class="overflow-x-auto">
        @if($attendanceByClassList->isEmpty())
        <p class="p-6 text-sm text-gray-500 text-center">Henüz yoklama kaydı yok.</p>
        @else
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sınıf</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Katılan</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Devamsız</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Oran</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($attendanceByClassList as $row)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $row['class_name'] }}</td>
                    <td class="px-4 py-3 text-sm text-right text-green-600">{{ $row['present'] }}</td>
                    <td class="px-4 py-3 text-sm text-right text-red-600">{{ $row['absent'] }}</td>
                    <td class="px-4 py-3 text-sm text-right">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $row['rate'] >= 70 ? 'bg-green-100 text-green-800' : ($row['rate'] >= 50 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">%{{ $row['rate'] }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

{{-- Son ödemeler & Son yoklamalar --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Son Ödemeler</h3>
            <a href="{{ route('admin.payments.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tümünü Gör</a>
        </div>
        <div class="overflow-x-auto max-h-80 overflow-y-auto">
            @if($recentPayments->isEmpty())
            <p class="p-6 text-sm text-gray-500 text-center">Henüz ödeme kaydı yok.</p>
            @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Öğrenci / Veli</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Tutar</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentPayments as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm text-gray-900">
                            @if($p->studentFee && $p->studentFee->student)
                                {{ $p->studentFee->student->first_name }} {{ $p->studentFee->student->last_name }}
                            @else
                                —
                            @endif
                            @if($p->parent && $p->parent->user)
                                <span class="text-gray-500 text-xs block">{{ $p->parent->user->name }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm text-right font-medium text-gray-900">{{ number_format($p->amount, 2) }} ₺</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $p->created_at ? $p->created_at->format('d.m.Y H:i') : '—' }}</td>
                        <td class="px-4 py-2">
                            @if($p->status === 'completed')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Tamamlandı</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">{{ $p->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Son Yoklamalar</h3>
            <a href="{{ route('admin.attendances.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tümünü Gör</a>
        </div>
        <div class="overflow-x-auto max-h-80 overflow-y-auto">
            @if($recentAttendances->isEmpty())
            <p class="p-6 text-sm text-gray-500 text-center">Henüz yoklama kaydı yok.</p>
            @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Öğrenci</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sınıf</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentAttendances as $a)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $a->student ? $a->student->first_name . ' ' . $a->student->last_name : '—' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $a->classModel ? $a->classModel->name : '—' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $a->attendance_date ? $a->attendance_date->format('d.m.Y') : '—' }}</td>
                        <td class="px-4 py-2">
                            @if($a->status === 'present')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Katıldı</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Devamsız</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>

{{-- Yaklaşan aidatlar --}}
@if($upcomingFees->isNotEmpty())
<div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">Yaklaşan Aidatlar (7 Gün)</h3>
        <a href="{{ route('admin.student-fees.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tüm Aidatlar</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Öğrenci</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vade Tarihi</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tutar</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($upcomingFees as $fee)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">
                        @if($fee->student)
                        <a href="{{ route('admin.students.show', $fee->student) }}" class="text-indigo-600 hover:text-indigo-800">{{ $fee->student->first_name }} {{ $fee->student->last_name }}</a>
                        @else
                        —
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $fee->due_date ? $fee->due_date->format('d.m.Y') : '—' }}</td>
                    <td class="px-4 py-3 text-sm text-right font-medium text-gray-900">{{ number_format($fee->amount, 2) }} ₺</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $fee->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}">{{ $fee->status === 'overdue' ? 'Gecikmiş' : 'Bekliyor' }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Hızlı işlemler --}}
<div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">Hızlı İşlemler</h3>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.students.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            <i class="fas fa-user-graduate mr-2"></i>Öğrenciler
        </a>
        <a href="{{ route('admin.classes.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
            <i class="fas fa-users mr-2"></i>Sınıflar
        </a>
        <a href="{{ route('admin.student-fees.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
            <i class="fas fa-lira-sign mr-2"></i>Aidatlar
        </a>
        <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
            <i class="fas fa-receipt mr-2"></i>Ödemeler
        </a>
        <a href="{{ route('admin.attendances.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
            <i class="fas fa-clipboard-check mr-2"></i>Yoklamalar
        </a>
        <a href="{{ route('admin.class-cancellations.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
            <i class="fas fa-calendar-times mr-2"></i>İptal / Telafi
        </a>
    </div>
</div>
@endsection
