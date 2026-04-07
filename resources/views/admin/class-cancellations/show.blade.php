@extends('layouts.panel')

@section('title', 'Ders İptal/Erteleme Detayı')
@section('page-title', 'Ders İptal/Erteleme Detayı')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.class-cancellations.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>
        Geri Dön
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
        <h2 class="text-xl font-bold text-white">İptal/Erteleme Detayı</h2>
    </div>

    <!-- Main Content -->
    <div class="p-6">
        <!-- İptal/Erteleme Bilgileri -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <i class="fas fa-calendar-times text-red-500 mr-2"></i>
                    <span class="text-sm font-medium text-gray-500">Tip</span>
                </div>
                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $classCancellation->cancellation_type === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $classCancellation->cancellation_type === 'cancelled' ? 'İptal' : 'Erteleme' }}
                </span>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    <span class="text-sm font-medium text-gray-500">Durum</span>
                </div>
                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $classCancellation->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $classCancellation->status === 'completed' ? 'Tamamlandı' : 'Bekliyor' }}
                </span>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <i class="fas fa-book text-indigo-500 mr-2"></i>
                    <span class="text-sm font-medium text-gray-500">Sınıf</span>
                </div>
                <p class="text-gray-900 font-semibold">{{ $classCancellation->classModel->name }}</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <i class="fas fa-user text-purple-500 mr-2"></i>
                    <span class="text-sm font-medium text-gray-500">İptal Eden</span>
                </div>
                <p class="text-gray-900">{{ $classCancellation->cancelledBy->name ?? 'Bilinmiyor' }}</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <i class="fas fa-calendar text-orange-500 mr-2"></i>
                    <span class="text-sm font-medium text-gray-500">Orijinal Tarih</span>
                </div>
                <p class="text-gray-900 font-semibold">{{ $classCancellation->original_date->format('d.m.Y') }}</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <i class="fas fa-calendar-check text-green-500 mr-2"></i>
                    <span class="text-sm font-medium text-gray-500">Yeni Tarih</span>
                </div>
                <p class="text-gray-900 font-semibold">{{ $classCancellation->new_date ? $classCancellation->new_date->format('d.m.Y') : 'Belirlenmedi' }}</p>
            </div>
        </div>

        <!-- Neden -->
        @if($classCancellation->reason)
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                <i class="fas fa-comment-alt text-gray-500 mr-2"></i>
                Neden
            </h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-700 whitespace-pre-wrap">{{ $classCancellation->reason }}</p>
            </div>
        </div>
        @endif

        <!-- Telafi Dersleri -->
        @if($classCancellation->makeupClasses->count() > 0)
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-redo text-indigo-500 mr-2"></i>
                Telafi Dersleri ({{ $classCancellation->makeupClasses->count() }})
            </h3>
            <div class="bg-gray-50 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Öğrenci</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Telafi Tarihi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($classCancellation->makeupClasses as $makeupClass)
                            @foreach($makeupClass->studentMakeupClasses as $studentMakeup)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $studentMakeup->student->name ?? 'Bilinmiyor' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $studentMakeup->scheduled_date ? $studentMakeup->scheduled_date->format('d.m.Y') : 'Belirlenmedi' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $studentMakeup->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($studentMakeup->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $studentMakeup->status === 'completed' ? 'Tamamlandı' : 
                                           ($studentMakeup->status === 'scheduled' ? 'Planlandı' : 'Bekliyor') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="mb-6">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-yellow-800 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Henüz telafi dersi oluşturulmamış.
                </p>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            @if(!$classCancellation->new_date)
            <button onclick="openScheduleCalendarModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                <i class="fas fa-calendar-check mr-2"></i>
                Yeni Tarih Belirle
            </button>
            @endif
            <a href="{{ route('admin.class-cancellations.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Geri Dön
            </a>
        </div>
    </div>
</div>

<!-- Takvim Modal -->
<div id="scheduleCalendarModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-6xl shadow-lg rounded-md bg-white" style="max-height: 90vh; overflow-y: auto;">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Telafi Dersi Tarihi ve Saati Belirle</h3>
            <button onclick="closeScheduleCalendarModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <div id="makeupScheduleCalendar"></div>
    </div>
</div>

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
#makeupScheduleCalendar {
    font-family: inherit;
}
.fc-theme-standard td, .fc-theme-standard th {
    border-color: #e5e7eb;
}
.fc-button {
    background: #4f46e5 !important;
    border: none !important;
    padding: 6px 12px !important;
    border-radius: 6px !important;
    font-weight: 500 !important;
}
.fc-button:hover {
    background: #4338ca !important;
}
.fc-event {
    cursor: pointer !important;
    border-radius: 4px !important;
    padding: 4px 6px !important;
    font-size: 12px !important;
    border: 2px solid !important;
    font-weight: 500 !important;
}
.fc-event:hover {
    opacity: 0.8 !important;
    transform: scale(1.02) !important;
}
.fc-timegrid-slot {
    cursor: pointer !important;
    transition: background-color 0.2s !important;
}
.fc-timegrid-slot:hover {
    background-color: #f0f9ff !important;
}
.fc-timegrid-event {
    margin: 1px 2px !important;
}
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/tr.global.min.js'></script>
<script>
let scheduleCalendar = null;
const cancellationId = {{ $classCancellation->id }};
const originalClassId = {{ $classCancellation->class_id }};
const originalDate = '{{ $classCancellation->original_date->format('Y-m-d') }}';

function openScheduleCalendarModal() {
    document.getElementById('scheduleCalendarModal').classList.remove('hidden');
    
    if (!scheduleCalendar) {
        const calendarEl = document.getElementById('makeupScheduleCalendar');
        
        const calendarEvents = @json($calendarEvents ?? []);
        
        console.log('Raw calendar events from PHP:', calendarEvents.length, calendarEvents);
        
        // Event'lerin end time'ını düzelt (dashboard'daki gibi)
        const eventsWithDuration = calendarEvents.map(event => {
            // Event zaten ISO formatında geliyor, sadece end time kontrolü yap
            if (event.start && !event.end) {
                const start = new Date(event.start);
                const end = new Date(start);
                end.setHours(end.getHours() + 1);
                end.setMinutes(end.getMinutes() + 30);
                event.end = end.toISOString();
            }
            return event;
        });
        
        console.log('Processed calendar events:', eventsWithDuration.length);
        if (eventsWithDuration.length > 0) {
            console.log('First event sample:', eventsWithDuration[0]);
        }
        
        scheduleCalendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            locale: 'tr',
            firstDay: 1,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Bugün',
                week: 'Hafta',
                day: 'Gün'
            },
            slotMinTime: '08:00:00',
            slotMaxTime: '22:00:00',
            slotDuration: '00:30:00',
            allDaySlot: false,
            height: 'auto',
            events: eventsWithDuration,
            eventDisplay: 'block',
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            eventContent: function(arg) {
                const title = arg.event.title;
                const time = arg.timeText || '';
                return {
                    html: `
                        <div style="padding: 2px 4px; font-size: 11px;">
                            <div style="font-weight: 600; margin-bottom: 1px;">${time}</div>
                            <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${title}</div>
                        </div>
                    `
                };
            },
            dateClick: function(info) {
                // Boş bir saate tıklandı
                const clickedDate = info.dateStr.split('T')[0];
                const clickedTime = info.dateStr.split('T')[1]?.substring(0, 5) || '10:00';
                
                // Saat seçim formu aç
                showTimeSelectionForm(clickedDate, clickedTime);
            },
            eventClick: function(info) {
                // Mevcut bir derse tıklandı
                const eventDate = info.event.start ? new Date(info.event.start).toISOString().split('T')[0] : '';
                const classId = info.event.extendedProps.class_id;
                const className = info.event.title;
                
                // Bu derse telafi olarak ekleme seçeneği sun
                showAddToExistingClassForm(eventDate, classId, className);
            }
        });
        
        scheduleCalendar.render();
    } else {
        scheduleCalendar.refetchEvents();
    }
}

function closeScheduleCalendarModal() {
    document.getElementById('scheduleCalendarModal').classList.add('hidden');
}

function showTimeSelectionForm(date, time) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center';
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">Yeni Telafi Dersi Oluştur</h3>
            <form id="newMakeupClassForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="new_date" value="${date}">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tarih</label>
                    <input type="date" value="${date}" disabled class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Saati *</label>
                    <input type="time" name="start_time" value="${time}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bitiş Saati *</label>
                    <input type="time" name="end_time" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        İptal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Oluştur
                    </button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    modal.querySelector('#newMakeupClassForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('_method', 'PUT');
        
        // Loading göster
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Oluşturuluyor...';
        
        fetch(`/admin/class-cancellations/${cancellationId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            // Önce response'un JSON olup olmadığını kontrol et
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // HTML response ise (validation error gibi)
                return response.text().then(text => {
                    throw new Error('HTML response received: ' + text.substring(0, 200));
                });
            }
        })
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect || '{{ route('admin.class-cancellations.index') }}';
            } else {
                // Validation errors
                if (data.errors) {
                    let errorMsg = 'Lütfen formu kontrol edin:\n';
                    for (const [key, messages] of Object.entries(data.errors)) {
                        errorMsg += `- ${messages.join(', ')}\n`;
                    }
                    alert(errorMsg);
                } else {
                    alert(data.message || 'Bir hata oluştu');
                }
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu: ' + (error.message || 'Bilinmeyen hata'));
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    });
    
    modal.onclick = function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    };
}

function showAddToExistingClassForm(date, classId, className) {
    if (confirm(`Bu derse (${className}) telafi olarak eklemek istediğinize emin misiniz?`)) {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PUT');
        formData.append('new_date', date);
        formData.append('scheduled_class_id', classId);
        
        fetch(`/admin/class-cancellations/${cancellationId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect || '{{ route('admin.class-cancellations.index') }}';
            } else {
                alert(data.message || 'Bir hata oluştu');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu');
        });
    }
}
</script>
@endpush
@endsection
