@extends('layouts.panel')

@section('title', 'Dashboard')
@section('page-title', 'Admin Dashboard')
@section('page-description', 'Okul yönetim paneli ve istatistikler')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
{{-- Hızlı işlemler --}}
<div class="mb-8">
    <h3 class="text-sm font-semibold text-slate-700 mb-3">Hızlı işlemler</h3>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.students.index') }}" class="inline-flex items-center px-4 py-2.5 bg-blue-100 text-blue-800 text-sm font-medium rounded-xl hover:bg-blue-200 transition-colors">
            <i class="fas fa-user-graduate mr-2"></i>Öğrenciler
        </a>
        <a href="{{ route('admin.classes.index') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-xl hover:bg-indigo-200 transition-colors">
            <i class="fas fa-book mr-2"></i>Sınıflar
        </a>
        <a href="{{ route('admin.student-fees.index') }}" class="inline-flex items-center px-4 py-2.5 bg-amber-100 text-amber-800 text-sm font-medium rounded-xl hover:bg-amber-200 transition-colors">
            <i class="fas fa-receipt mr-2"></i>Aidatlar
        </a>
        <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2.5 bg-green-100 text-green-800 text-sm font-medium rounded-xl hover:bg-green-200 transition-colors">
            <i class="fas fa-credit-card mr-2"></i>Ödemeler
        </a>
        <a href="{{ route('admin.attendances.index') }}" class="inline-flex items-center px-4 py-2.5 bg-emerald-100 text-emerald-800 text-sm font-medium rounded-xl hover:bg-emerald-200 transition-colors">
            <i class="fas fa-check-circle mr-2"></i>Yoklamalar
        </a>
        <a href="{{ route('admin.coaches.index') }}" class="inline-flex items-center px-4 py-2.5 bg-violet-100 text-violet-800 text-sm font-medium rounded-xl hover:bg-violet-200 transition-colors">
            <i class="fas fa-user-tie mr-2"></i>Antrenörler
        </a>
        <a href="{{ route('admin.school-settings.index') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-100 text-slate-800 text-sm font-medium rounded-xl hover:bg-slate-200 transition-colors">
            <i class="fas fa-cog mr-2"></i>Ayarlar
        </a>
    </div>
</div>

{{-- Özet KPI kartları: 1. satır 4 kart, 2. satır 3 kart --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                <i class="fas fa-user-graduate text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Aktif Öğrenci</p>
                <p class="text-xl font-bold text-slate-800">{{ $stats['active_students'] }}</p>
                <p class="text-xs text-slate-500">{{ $stats['total_students'] }} toplam</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center text-green-600">
                <i class="fas fa-lira-sign text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Bu Ay Toplanan</p>
                <p class="text-lg font-bold text-green-700 truncate" title="{{ number_format($stats['monthly_revenue'], 2, ',', '.') }} ₺">{{ number_format($stats['monthly_revenue'], 2, ',', '.') }} ₺</p>
                <p class="text-xs text-slate-500">{{ number_format($stats['total_revenue'], 2, ',', '.') }} ₺ toplam</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                <i class="fas fa-book text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Aktif Sınıf</p>
                <p class="text-xl font-bold text-indigo-700">{{ $stats['active_classes'] }}</p>
                <p class="text-xs text-slate-500">{{ $stats['total_classes'] }} toplam</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600">
                <i class="fas fa-user-tie text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Antrenör / Veli</p>
                <p class="text-xl font-bold text-slate-800">{{ $stats['total_coaches'] }} / {{ $stats['total_parents'] }}</p>
                <p class="text-xs text-slate-500">Aktif antrenör ve veli</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                <i class="fas fa-exclamation-triangle text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Bekleyen Aidat</p>
                <p class="text-lg font-bold text-amber-700">{{ number_format($stats['pending_fees'], 2, ',', '.') }} ₺</p>
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
                <p class="text-xs text-slate-500">Yoklama kaydı</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600">
                <i class="fas fa-users text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Toplam Öğrenci</p>
                <p class="text-xl font-bold text-slate-800">{{ $stats['total_students'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Ders Takvimi --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm mb-8">
    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-semibold text-slate-800">Ders Takvimi</h3>
            <p class="text-xs text-slate-500 mt-0.5">Okulunuzdaki tüm derslerin programı — derslere tıklayarak detay görebilirsiniz</p>
        </div>
        <span class="text-xs text-slate-500"><i class="fas fa-info-circle mr-1"></i>Her ders farklı renkte gösterilir</span>
    </div>
    <div class="p-4">
        <div id="adminCalendar"></div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
    {{-- Yaklaşan Vade Tarihleri --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-800">Yaklaşan Vade Tarihleri (7 Gün)</h3>
                <p class="text-xs text-slate-500 mt-0.5">Bekleyen aidatlar</p>
            </div>
            <a href="{{ route('admin.student-fees.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tümü →</a>
        </div>
        <div class="overflow-x-auto">
            @if($upcoming_fees->count() > 0)
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Öğrenci</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Plan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tutar</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Vade</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($upcoming_fees as $fee)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $fee->student->first_name ?? '' }} {{ $fee->student->last_name ?? '' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $fee->fee_label }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ number_format($fee->amount, 2, ',', '.') }} ₺</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $fee->due_date->format('d.m.Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-8 text-center text-slate-500 text-sm">Yaklaşan vadesi olan aidat yok.</div>
            @endif
        </div>
    </div>

    {{-- Son Eklenen Öğrenciler --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-800">Son Eklenen Öğrenciler</h3>
                <p class="text-xs text-slate-500 mt-0.5">En son 5 kayıt</p>
            </div>
            <a href="{{ route('admin.students.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tümü →</a>
        </div>
        <div class="p-4">
            @if($recent_students->count() > 0)
            <div class="space-y-2">
                @foreach($recent_students as $student)
                <a href="{{ route('admin.students.show', $student) }}" class="flex items-center justify-between px-4 py-3 rounded-xl hover:bg-slate-50 transition-colors">
                    <div>
                        <p class="font-medium text-slate-800">{{ $student->first_name }} {{ $student->last_name }}</p>
                        <p class="text-xs text-slate-500">{{ $student->classModel->name ?? 'Sınıf atanmamış' }}</p>
                    </div>
                    <div class="text-right flex items-center gap-2">
                        <span class="text-xs text-slate-500">{{ $student->created_at->format('d.m.Y') }}</span>
                        @if($student->is_active)
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-slate-100 text-slate-600">Pasif</span>
                        @endif
                        <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="py-8 text-center text-slate-500 text-sm">Henüz öğrenci kaydı yok.</div>
            @endif
        </div>
    </div>
</div>

{{-- Son Ödemeler --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm mb-8">
    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-base font-semibold text-slate-800">Son Ödemeler</h3>
            <p class="text-xs text-slate-500 mt-0.5">Tamamlanan ödemeler</p>
        </div>
        <a href="{{ route('admin.payments.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tüm ödemeler →</a>
    </div>
    <div class="overflow-x-auto">
        @if($recent_payments->count() > 0)
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Öğrenci</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Veli</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tutar</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Durum</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tarih</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($recent_payments as $payment)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-4 py-3 text-sm font-medium text-slate-800">
                        {{ $payment->studentFee && $payment->studentFee->student ? $payment->studentFee->student->first_name . ' ' . $payment->studentFee->student->last_name : '—' }}
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $payment->parent && $payment->parent->user ? $payment->parent->user->name : '—' }}</td>
                    <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ number_format($payment->amount, 2, ',', '.') }} ₺</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $payment->status === 'completed' ? 'Tamamlandı' : ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-8 text-center text-slate-500 text-sm">Henüz ödeme bulunmamaktadır.</div>
        @endif
    </div>
</div>

@push('styles')
<style>
/* Takvim Genel Stilleri */
#adminCalendar {
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

.fc-button-primary:not(:disabled):active {
    background: #312e81 !important;
}

/* Modal/Popover Stilleri */
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
const cancellationStoreUrl = '{{ route("admin.class-cancellations.store") }}';

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('adminCalendar');
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
            const eventDate = info.event.start ? new Date(info.event.start).toISOString().split('T')[0] : '';
            const todayStr = new Date().toISOString().split('T')[0];
            const isPast = eventDate < todayStr;
            
            showEventModal({
                title: props.full_class_name || info.event.title,
                time: endTime ? `${startTime} - ${endTime}` : startTime,
                sport: props.sport || '-',
                branch: props.branch || '-',
                coach: props.coach || '-',
                students: props.is_makeup ? '-' : `${props.students || 0} / ${props.capacity || 0}`,
                day: props.day || '-',
                classId: props.class_id || null,
                eventDate: eventDate,
                isMakeup: props.is_makeup || false,
                makeupSessionId: props.makeup_session_id || null,
                isPast: isPast
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
                ${data.isMakeup ? '<div class="event-info-item"><span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Telafi Dersi</span></div>' : ''}
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
                    <div class="event-info-icon" style="background: #fce7f3;">
                        <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="event-info-label">Antrenör</div>
                    <div class="event-info-value">${data.coach}</div>
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
            <div style="padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; gap: 12px; justify-content: flex-end;">
                ${data.isMakeup && data.makeupSessionId ? `<a href="{{ url('admin/makeup-sessions') }}/${data.makeupSessionId}" style="padding: 8px 16px; background: #8B5CF6; color: white; border: none; border-radius: 6px; text-decoration: none; font-weight: 500;">Telafi Dersi Detayı</a>` : ''}
                ${!data.isMakeup && data.classId && !data.isPast ? `
                <button onclick="openCancelModal(${data.classId}, '${data.eventDate}')" style="padding: 8px 16px; background: #EF4444; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; transition: all 0.2s;">
                    <i class="fas fa-times mr-2"></i>İptal Et
                </button>
                ` : ''}
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.style.display = 'block';
    
    // Kapatma işlevleri
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

// İptal/Erteleme Modal Fonksiyonları
function openCancelModal(classId, eventDate) {
    // Mevcut modal'ı kapat
    document.querySelectorAll('.event-modal').forEach(m => {
        m.style.display = 'none';
        setTimeout(() => m.remove(), 300);
    });
    
    // İptal modal'ını aç
    showCancelPostponeModal('cancelled', classId, eventDate);
}

function openPostponeModal(classId, eventDate) {
    // Mevcut modal'ı kapat
    document.querySelectorAll('.event-modal').forEach(m => {
        m.style.display = 'none';
        setTimeout(() => m.remove(), 300);
    });
    
    // Erteleme modal'ını aç
    showCancelPostponeModal('postponed', classId, eventDate);
}

function showCancelPostponeModal(type, classId, eventDate) {
    const modal = document.createElement('div');
    modal.className = 'event-modal';
    const isCancel = type === 'cancelled';
    modal.innerHTML = `
        <div class="event-modal-content" style="max-width: 600px;">
            <div class="event-modal-header" style="background: ${isCancel ? 'linear-gradient(135deg, #EF4444 0%, #DC2626 100%)' : 'linear-gradient(135deg, #F59E0B 0%, #D97706 100%)'};">
                <h3 style="margin: 0; font-size: 18px; font-weight: 600;">Ders ${isCancel ? 'İptal' : 'Ertele'}</h3>
                <span class="event-modal-close">&times;</span>
            </div>
            <form id="cancelPostponeForm" style="padding: 24px;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="class_id" value="${classId}">
                <input type="hidden" name="cancellation_type" value="${type}">
                <input type="hidden" name="original_date" value="${eventDate}">
                
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #374151;">Orijinal Ders Tarihi</label>
                    <input type="date" value="${eventDate}" disabled style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; background: #f3f4f6;">
                </div>
                
                ${!isCancel ? `
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #374151;">Yeni Tarih (Opsiyonel)</label>
                    <input type="date" name="new_date" id="new_date" min="${new Date().toISOString().split('T')[0]}" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;">
                    <p style="margin-top: 4px; font-size: 12px; color: #6b7280;">Boş bırakırsanız daha sonra belirlenebilir.</p>
                </div>
                ` : ''}
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #374151;">Neden</label>
                    <textarea name="reason" rows="3" placeholder="İptal/erteleme nedeni..." style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical;"></textarea>
                </div>
                
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" onclick="closeCancelPostponeModal()" style="padding: 10px 20px; background: #e5e7eb; color: #374151; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                        İptal
                    </button>
                    <button type="submit" style="padding: 10px 20px; background: ${isCancel ? '#EF4444' : '#F59E0B'}; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                        Kaydet
                    </button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.style.display = 'block';
    
    // Form submit
    modal.querySelector('#cancelPostponeForm').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        // Loading state
        submitBtn.disabled = true;
        submitBtn.textContent = 'Kaydediliyor...';
        
        // CSRF token kontrolü
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        if (!formData.has('_token')) {
            formData.append('_token', csrfToken);
        }
        
        console.log('Sending to:', cancellationStoreUrl);
        console.log('FormData:', Object.fromEntries(formData));
        
        fetch(cancellationStoreUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            
            // JSON response kontrolü
            if (contentType && contentType.includes('application/json')) {
                const data = await response.json();
                if (!response.ok) {
                    // Validation hataları veya diğer hatalar
                    const errorMessage = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Bir hata oluştu');
                    throw new Error(errorMessage);
                }
                return data;
            }
            
            // HTML response geldiyse (redirect olmuş olabilir)
            if (response.ok) {
                // Başarılı HTML response - muhtemelen redirect oldu
                return { success: true, redirect: '/admin/class-cancellations' };
            }
            
            // Diğer durumlar
            const text = await response.text();
            throw new Error('Beklenmeyen yanıt: ' + (text.substring(0, 100) || 'Bilinmeyen hata'));
        })
        .then(data => {
            if (data.success) {
                // Başarı mesajı göster
                alert(data.message || 'İşlem başarıyla tamamlandı');
                // Modal'ı kapat
                closeCancelPostponeModal();
                // Sayfayı yenile veya yönlendir
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            } else {
                throw new Error(data.message || 'Bilinmeyen hata');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Bir hata oluştu. Lütfen tekrar deneyin.');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    };
    
    // Kapatma işlevleri
    modal.querySelector('.event-modal-close').onclick = () => {
        closeCancelPostponeModal();
    };
    
    modal.onclick = (e) => {
        if (e.target === modal) {
            closeCancelPostponeModal();
        }
    };
    
    window.closeCancelPostponeModal = function() {
        modal.style.display = 'none';
        setTimeout(() => modal.remove(), 300);
    };
}
</script>
@endpush
@endsection
