@extends('layouts.panel')

@section('title', 'Dashboard')
@section('page-title', 'Antrenör Dashboard')
@section('page-description', 'Sınıflarınız ve öğrencileriniz hakkında genel bakış')

@section('sidebar-menu')
@include('coach.partials.sidebar')
@endsection

@section('content')
@php
    $todayMakeupSessions = $todayMakeupSessions ?? collect();
    $stats = $stats ?? ['total_classes' => 0, 'total_students' => 0, 'total_attendances' => 0, 'monthly_attendances' => 0, 'total_progress' => 0];
    $coach = \App\Models\Coach::where('user_id', Auth::id())->first();
    $coachSchool = $coach?->school;
@endphp

{{-- Hızlı işlemler --}}
<div class="mb-8">
    <h3 class="text-sm font-semibold text-slate-700 mb-3">Hızlı işlemler</h3>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('coach.classes.index') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-xl hover:bg-indigo-200 transition-colors">
            <i class="fas fa-book mr-2"></i>Sınıflarım
        </a>
        <a href="{{ route('coach.attendances.create') }}" class="inline-flex items-center px-4 py-2.5 bg-emerald-100 text-emerald-800 text-sm font-medium rounded-xl hover:bg-emerald-200 transition-colors">
            <i class="fas fa-check-square mr-2"></i>Yoklama Al
        </a>
        <a href="{{ route('coach.attendances.index') }}" class="inline-flex items-center px-4 py-2.5 bg-blue-100 text-blue-800 text-sm font-medium rounded-xl hover:bg-blue-200 transition-colors">
            <i class="fas fa-history mr-2"></i>Yoklama Geçmişi
        </a>
        <a href="{{ route('coach.student-progress.index') }}" class="inline-flex items-center px-4 py-2.5 bg-violet-100 text-violet-800 text-sm font-medium rounded-xl hover:bg-violet-200 transition-colors">
            <i class="fas fa-chart-line mr-2"></i>Gelişim Notları
        </a>
        <a href="{{ route('coach.media.index') }}" class="inline-flex items-center px-4 py-2.5 bg-amber-100 text-amber-800 text-sm font-medium rounded-xl hover:bg-amber-200 transition-colors">
            <i class="fas fa-images mr-2"></i>Paylaşımlarım
        </a>
        @if(($coachSchool ?? null) && $coachSchool->makeup_class_enabled)
        <a href="{{ route('coach.makeup-sessions.index') }}" class="inline-flex items-center px-4 py-2.5 bg-purple-100 text-purple-800 text-sm font-medium rounded-xl hover:bg-purple-200 transition-colors">
            <i class="fas fa-calendar-plus mr-2"></i>Telafi Derslerim
        </a>
        @endif
    </div>
</div>

{{-- Özet KPI kartları --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                <i class="fas fa-book text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Atanan Sınıf</p>
                <p class="text-xl font-bold text-slate-800">{{ $stats['total_classes'] }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center text-green-600">
                <i class="fas fa-user-graduate text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Toplam Öğrenci</p>
                <p class="text-xl font-bold text-green-700">{{ $stats['total_students'] }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                <i class="fas fa-check-circle text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Toplam Katılım</p>
                <p class="text-xl font-bold text-slate-800">{{ $stats['total_attendances'] }}</p>
                <p class="text-xs text-slate-500">{{ $stats['monthly_attendances'] }} bu ay</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600">
                <i class="fas fa-chart-line text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Gelişim Notları</p>
                <p class="text-xl font-bold text-violet-700">{{ $stats['total_progress'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Bugün — Takvimin üstünde, minimal kartlar --}}
<div class="mb-8">
    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-3">{{ now()->locale('tr')->translatedFormat('d F Y') }}</p>
    <div class="grid grid-cols-1 {{ ($todayMakeupSessions->count() > 0) ? 'md:grid-cols-2' : '' }} gap-4">
        {{-- Bugünkü Dersler kartı --}}
        <div class="rounded-2xl border border-slate-100 bg-white/80 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-50">
                <h3 class="text-sm font-medium text-slate-700">Dersleriniz</h3>
            </div>
            <div class="p-4">
                @if(($todayClasses ?? collect())->count() > 0)
                <ul class="space-y-1">
                    @foreach($todayClasses as $class)
                    @php
                        $todayDay = strtolower(now()->format('l'));
                        $schedule = $class->class_schedule[$todayDay] ?? null;
                        $startTime = is_array($schedule) ? ($schedule['start_time'] ?? null) : ($schedule ?? null);
                        $endTime = is_array($schedule) ? ($schedule['end_time'] ?? null) : null;
                        $timeDisplay = $startTime ? ($endTime ? "{$startTime}–{$endTime}" : $startTime) : '—';
                    @endphp
                    <li class="flex items-center gap-3 py-2.5 px-3 rounded-xl hover:bg-slate-50/70 transition-colors">
                        <span class="text-xs font-medium text-slate-400 tabular-nums w-14 flex-shrink-0">{{ $timeDisplay }}</span>
                        <span class="flex-1 min-w-0">
                            <span class="text-sm font-medium text-slate-800">{{ $class->name }}</span>
                            <span class="text-xs text-slate-400 ml-1.5">· {{ $class->sportBranch->name ?? 'N/A' }}</span>
                        </span>
                        <span class="text-xs text-slate-400 flex-shrink-0">{{ $class->students->count() }} öğr.</span>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-sm text-slate-400 py-6 text-center">Bugün dersiniz yok</p>
                @endif
            </div>
        </div>

        {{-- Bugünkü Telafi Dersleri kartı --}}
        @if($todayMakeupSessions->count() > 0)
        <div class="rounded-2xl border border-slate-100 bg-white/80 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-50">
                <h3 class="text-sm font-medium text-slate-700">Telafi Dersleri</h3>
            </div>
            <div class="p-4">
                <ul class="space-y-1">
                    @foreach($todayMakeupSessions as $session)
                    <li class="flex items-center gap-3 py-2.5 px-3 rounded-xl hover:bg-violet-50/50 transition-colors">
                        <span class="text-xs font-medium text-slate-400 tabular-nums w-14 flex-shrink-0">
                            {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="text-sm font-medium text-slate-800">{{ $session->name ?? 'Telafi Dersi' }}</span>
                            @if($session->branch)
                            <span class="text-xs text-slate-400 ml-1.5">· {{ $session->branch->name }}</span>
                            @endif
                        </span>
                        <span class="text-xs text-slate-400 flex-shrink-0">{{ $session->student_makeup_classes_count ?? 0 }} öğr.</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Ders Takvimi --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm mb-8">
    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
        <h3 class="text-base font-semibold text-slate-800">Ders Takvimi</h3>
        <p class="text-xs text-slate-500 mt-0.5">Size atanan derslerin takvimi</p>
    </div>
    <div class="p-4">
        <div id="coachCalendar"></div>
    </div>
</div>

{{-- Son Gelişim Notları --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm mb-8">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-800">Son Gelişim Notları</h3>
                <p class="text-xs text-slate-500 mt-0.5">En son 5 kayıt</p>
            </div>
            <a href="{{ route('coach.student-progress.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tümü →</a>
        </div>
        <div class="p-4">
            @if(($recent_progress ?? collect())->count() > 0)
            <div class="space-y-2">
                @foreach($recent_progress as $progress)
                <div class="px-4 py-3 rounded-xl border border-slate-100 hover:bg-slate-50/50 transition-colors">
                    <div class="flex justify-between items-start gap-2">
                        <div class="min-w-0">
                            <p class="font-medium text-slate-800">{{ $progress->student->first_name ?? '' }} {{ $progress->student->last_name ?? '' }}</p>
                            <p class="text-xs text-slate-500">{{ $progress->classModel->name ?? '-' }}</p>
                            <p class="text-sm text-slate-600 mt-1">{{ Str::limit($progress->notes ?? '', 80) }}</p>
                        </div>
                        <span class="text-xs text-slate-400 flex-shrink-0">{{ $progress->progress_date->format('d.m.Y') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-8 text-center text-slate-500 text-sm">Henüz gelişim notu bulunmamaktadır.</div>
            @endif
        </div>
</div>

{{-- Son Yoklamalar --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm mb-8">
    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-base font-semibold text-slate-800">Son Yoklamalar</h3>
            <p class="text-xs text-slate-500 mt-0.5">En son 10 kayıt</p>
        </div>
        <a href="{{ route('coach.attendances.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tümü →</a>
    </div>
    <div class="overflow-x-auto">
        @if(($recent_attendances ?? collect())->count() > 0)
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Öğrenci</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Sınıf</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tarih</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Durum</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($recent_attendances as $attendance)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $attendance->student->first_name ?? '' }} {{ $attendance->student->last_name ?? '' }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $attendance->classModel->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $attendance->attendance_date->format('d.m.Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $attendance->status === 'present' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $attendance->status === 'present' ? 'Katıldı' : 'Devamsız' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-8 text-center text-slate-500 text-sm">Henüz yoklama kaydı bulunmamaktadır.</div>
        @endif
    </div>
</div>

@push('styles')
<style>
/* Takvim Genel Stilleri */
#coachCalendar {
    font-family: inherit;
}

.fc-theme-standard td, .fc-theme-standard th {
    border-color: #e5e7eb;
}

.fc-daygrid-day-frame {
    min-height: 100px;
}

.fc-col-header-cell {
    padding: 12px 8px;
    background: #f9fafb;
    font-weight: 600;
    color: #374151;
    text-transform: capitalize;
}

.fc-day-today {
    background-color: #eff6ff !important;
}

.fc-day-today .fc-daygrid-day-number {
    color: #2563eb;
    font-weight: 700;
}

/* Event Stilleri */
.fc-event {
    border: none !important;
    border-radius: 6px !important;
    padding: 6px 8px !important;
    margin: 2px 0 !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
}

.fc-event:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15) !important;
    z-index: 10 !important;
}

.fc-event-title {
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    display: block !important;
    line-height: 1.4 !important;
}

.fc-daygrid-event {
    white-space: nowrap !important;
    overflow: visible !important;
}

/* Buton Stilleri */
.fc-button {
    background: #4f46e5 !important;
    border: none !important;
    padding: 8px 16px !important;
    border-radius: 6px !important;
    font-weight: 500 !important;
    transition: all 0.2s ease !important;
}

.fc-button:hover {
    background: #4338ca !important;
    transform: translateY(-1px);
}

.fc-button-active {
    background: #312e81 !important;
}

/* Modal Stilleri */
.event-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    animation: fadeIn 0.2s ease;
}

.event-modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    animation: slideDown 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideDown {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.event-modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px 12px 0 0;
    color: white;
}

.event-modal-body {
    padding: 24px;
}

.event-modal-close {
    color: white;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.event-modal-close:hover {
    opacity: 1;
}

.event-info-item {
    display: flex;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
}

.event-info-item:last-child {
    border-bottom: none;
}

.event-info-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    flex-shrink: 0;
}

.event-info-label {
    font-size: 13px;
    color: #6b7280;
    font-weight: 500;
    min-width: 100px;
}

.event-info-value {
    font-size: 14px;
    color: #111827;
    font-weight: 600;
    flex: 1;
}
</style>
@endpush

@push('scripts')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/tr.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('coachCalendar');
    const calendarEvents = @json($calendarEvents ?? []);

    // Event'lerin end time'ını 1.5 saat sonrası olarak ayarla
    const eventsWithDuration = calendarEvents.map(event => {
        if (event.start && !event.end) {
            const start = new Date(event.start);
            const end = new Date(start);
            end.setHours(end.getHours() + 1);
            end.setMinutes(end.getMinutes() + 30);
            event.end = end.toISOString();
        }
        return event;
    });

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'tr',
        firstDay: 1, // Pazartesi ile başla
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Bugün',
            month: 'Ay',
            week: 'Hafta',
            day: 'Gün'
        },
        dayHeaderFormat: { weekday: 'long' },
        events: eventsWithDuration,
        eventDisplay: 'block',
        eventContent: function(arg) {
            const title = arg.event.title;
            const time = arg.timeText || '';
            return {
                html: `
                    <div class="fc-event-main-frame">
                        <div class="fc-event-time" style="font-size: 10px; opacity: 0.9; margin-bottom: 2px; font-weight: 600;">${time}</div>
                        <div class="fc-event-title-container">
                            <div class="fc-event-title">${title}</div>
                        </div>
                    </div>
                `
            };
        },
        eventClick: function(info) {
            const props = info.event.extendedProps;
            const startTime = info.event.start ? new Date(info.event.start).toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' }) : '';
            const endTime = info.event.end ? new Date(info.event.end).toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' }) : '';
            
            showEventModal({
                title: props.full_class_name || info.event.title,
                time: endTime ? `${startTime} - ${endTime}` : startTime,
                sport: props.sport || '-',
                branch: props.branch || '-',
                students: `${props.students || 0} / ${props.capacity || 0}`,
                day: props.day || '-'
            });
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }
    });

    calendar.render();
});

// Event Modal Fonksiyonu
function showEventModal(data) {
    const modal = document.createElement('div');
    modal.className = 'event-modal';
    modal.innerHTML = `
        <div class="event-modal-content">
            <div class="event-modal-header">
                <h3 style="margin: 0; font-size: 18px; font-weight: 600;">${data.title}</h3>
                <span class="event-modal-close">&times;</span>
            </div>
            <div class="event-modal-body">
                <div class="event-info-item">
                    <div class="event-info-icon" style="background: #dbeafe;">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="event-info-label">Saat</div>
                    <div class="event-info-value">${data.time}</div>
                </div>
                <div class="event-info-item">
                    <div class="event-info-icon" style="background: #fef3c7;">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <div class="event-info-label">Branş</div>
                    <div class="event-info-value">${data.sport}</div>
                </div>
                <div class="event-info-item">
                    <div class="event-info-icon" style="background: #e0e7ff;">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="event-info-label">Şube</div>
                    <div class="event-info-value">${data.branch}</div>
                </div>
                <div class="event-info-item">
                    <div class="event-info-icon" style="background: #d1fae5;">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="event-info-label">Öğrenci</div>
                    <div class="event-info-value">${data.students}</div>
                </div>
                <div class="event-info-item">
                    <div class="event-info-icon" style="background: #fef3c7;">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="event-info-label">Gün</div>
                    <div class="event-info-value">${data.day}</div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.style.display = 'block';
    
    modal.querySelector('.event-modal-close').onclick = () => {
        modal.style.display = 'none';
        setTimeout(() => modal.remove(), 300);
    };
    
    modal.onclick = (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            setTimeout(() => modal.remove(), 300);
        }
    };
}
</script>
@endpush
@endsection
