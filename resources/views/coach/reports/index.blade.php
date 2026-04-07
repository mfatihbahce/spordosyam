@extends('layouts.panel')

@section('title', 'Raporlarım')
@section('page-title', 'Raporlarım')

@section('sidebar-menu')
    @include('coach.partials.sidebar')
@endsection

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">Raporlarım</h2>
    <p class="text-sm text-gray-500 mt-1">Yoklama, gelişim notları ve paylaşımlarınıza ait özet istatistikler.</p>
</div>

{{-- KPI Kartları --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    <a href="{{ route('coach.attendances.index') }}" class="bg-white rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg hover:border-indigo-100 transition-all group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Toplam Yoklama</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_attendances']) }}</p>
                <p class="text-xs text-gray-500 mt-1">Katılan: {{ $stats['present_count'] }} · Devamsız: {{ $stats['absent_count'] }}</p>
            </div>
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                <i class="fas fa-check-circle text-indigo-600 text-xl"></i>
            </div>
        </div>
    </a>
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Katılım Oranı</p>
                <p class="text-2xl font-bold {{ $stats['attendance_rate'] >= 70 ? 'text-green-600' : ($stats['attendance_rate'] >= 50 ? 'text-amber-600' : 'text-red-600') }} mt-1">%{{ $stats['attendance_rate'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Tüm yoklamalar bazında</p>
            </div>
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-chart-pie text-emerald-600 text-xl"></i>
            </div>
        </div>
    </div>
    <a href="{{ route('coach.student-progress.index') }}" class="bg-white rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg hover:border-green-100 transition-all group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Gelişim Notu</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_progress']) }}</p>
                <p class="text-xs text-gray-500 mt-1">Öğrenci gelişim kayıtları</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition-colors">
                <i class="fas fa-chart-line text-green-600 text-xl"></i>
            </div>
        </div>
    </a>
    <a href="{{ route('coach.classes.index') }}" class="bg-white rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg hover:border-violet-100 transition-all group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Aktif Sınıf</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_classes'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['total_students'] }} öğrenci</p>
            </div>
            <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center group-hover:bg-violet-200 transition-colors">
                <i class="fas fa-users text-violet-600 text-xl"></i>
            </div>
        </div>
    </a>
    <a href="{{ route('coach.media.index') }}" class="bg-white rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg hover:border-sky-100 transition-all group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Paylaşım</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_media'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Yüklediğiniz içerik</p>
            </div>
            <div class="w-12 h-12 bg-sky-100 rounded-xl flex items-center justify-center group-hover:bg-sky-200 transition-colors">
                <i class="fas fa-photo-video text-sky-600 text-xl"></i>
            </div>
        </div>
    </a>
    <a href="{{ route('coach.makeup-sessions.index') }}" class="bg-white rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg hover:border-amber-100 transition-all group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Telafi Dersi</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_makeup_sessions'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['upcoming_makeup_sessions'] }} yaklaşan</p>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                <i class="fas fa-calendar-alt text-amber-600 text-xl"></i>
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
                    <div class="bg-indigo-500 rounded-t transition-all" style="height: {{ $maxVal > 0 ? round(($day['total'] / $maxVal) * 70) : 0 }}px;" title="{{ $day['label'] }}: {{ $day['present'] }} katıldı, {{ $day['absent'] }} devamsız"></div>
                </div>
                <span class="text-xs font-medium text-gray-600">{{ $day['label'] }}</span>
                <span class="text-xs text-gray-400">{{ $day['present'] }}/{{ $day['total'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Sınıfa göre özet --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Sınıfa Göre Yoklama</h3>
                <p class="text-sm text-gray-500 mt-0.5">Katılan / devamsız dağılımı</p>
            </div>
            <a href="{{ route('coach.attendances.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tümünü Gör</a>
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
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Sınıfa Göre Gelişim Notu</h3>
                <p class="text-sm text-gray-500 mt-0.5">Not sayısı dağılımı</p>
            </div>
            <a href="{{ route('coach.student-progress.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tümünü Gör</a>
        </div>
        <div class="overflow-x-auto">
            @if($progressByClassList->isEmpty())
            <p class="p-6 text-sm text-gray-500 text-center">Henüz gelişim notu yok.</p>
            @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sınıf</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Not Sayısı</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($progressByClassList as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $row['class_name'] }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-600">{{ $row['count'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>

{{-- Son yoklamalar & Son gelişim notları --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Son Yoklamalar</h3>
            <a href="{{ route('coach.attendances.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Yoklama Geçmişi</a>
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
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Son Gelişim Notları</h3>
            <a href="{{ route('coach.student-progress.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tümünü Gör</a>
        </div>
        <div class="p-4 max-h-80 overflow-y-auto">
            @if($recentProgress->isEmpty())
            <p class="text-sm text-gray-500 text-center py-4">Henüz gelişim notu yok.</p>
            @else
            <ul class="space-y-3">
                @foreach($recentProgress as $p)
                <li class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 border border-gray-100">
                    <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-chart-line text-green-600 text-sm"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $p->student ? $p->student->first_name . ' ' . $p->student->last_name : '—' }}</p>
                        <p class="text-xs text-gray-500">{{ $p->classModel ? $p->classModel->name : '—' }} · {{ $p->progress_date ? $p->progress_date->format('d.m.Y') : '—' }}</p>
                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ Str::limit($p->notes, 80) }}</p>
                    </div>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>

{{-- Hızlı işlemler --}}
<div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">Hızlı İşlemler</h3>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('coach.attendances.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            <i class="fas fa-clipboard-check mr-2"></i>Yoklama Al
        </a>
        <a href="{{ route('coach.attendances.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
            <i class="fas fa-history mr-2"></i>Yoklama Geçmişi
        </a>
        <a href="{{ route('coach.student-progress.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
            <i class="fas fa-plus mr-2"></i>Gelişim Notu Ekle
        </a>
        <a href="{{ route('coach.media.create') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
            <i class="fas fa-upload mr-2"></i>Paylaşım Yükle
        </a>
        <a href="{{ route('coach.makeup-sessions.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
            <i class="fas fa-calendar-alt mr-2"></i>Telafi Derslerim
        </a>
    </div>
</div>
@endsection
