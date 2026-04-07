@extends('layouts.panel')

@section('title', 'Ders İptal/Ertele')
@section('page-title', 'Ders İptal/Ertele')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Takvim -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Takvimden Seç</h3>
        <p class="text-sm text-gray-500 mb-4">Takvimden ders seçerek iptal/erteleme yapabilirsiniz</p>
        <div id="cancellationCalendar"></div>
    </div>
    
    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Manuel Giriş</h3>
        <form action="{{ route('admin.class-cancellations.store') }}" method="POST" id="cancellationForm">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">Sınıf *</label>
                <select name="class_id" id="class_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Sınıf Seçiniz</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
                @error('class_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="cancellation_type" class="block text-sm font-medium text-gray-700 mb-2">İşlem Tipi *</label>
                <select name="cancellation_type" id="cancellation_type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Seçiniz</option>
                    <option value="cancelled" {{ old('cancellation_type') === 'cancelled' ? 'selected' : '' }}>İptal</option>
                    <option value="postponed" {{ old('cancellation_type') === 'postponed' ? 'selected' : '' }}>Erteleme</option>
                </select>
                @error('cancellation_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="original_date" class="block text-sm font-medium text-gray-700 mb-2">Orijinal Ders Tarihi *</label>
                <input type="date" name="original_date" id="original_date" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                       value="{{ old('original_date') }}">
                @error('original_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="new_date" class="block text-sm font-medium text-gray-700 mb-2">Yeni Tarih (Opsiyonel)</label>
                <input type="date" name="new_date" id="new_date"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                       value="{{ old('new_date') }}"
                       min="{{ date('Y-m-d') }}">
                <p class="mt-1 text-xs text-gray-500">Erteleme durumunda yeni tarihi belirleyebilirsiniz. Boş bırakırsanız daha sonra belirlenebilir.</p>
                @error('new_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Neden</label>
                <textarea name="reason" id="reason" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="İptal/erteleme nedeni...">{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.class-cancellations.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                İptal
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Kaydet
            </button>
        </div>
    </form>
    </div>
</div>

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
#cancellationCalendar {
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
    font-size: 11px !important;
}
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/tr.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('cancellationCalendar');
    const calendarEvents = @json($calendarEvents ?? []);
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'tr',
        firstDay: 1,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth'
        },
        buttonText: {
            today: 'Bugün',
            month: 'Ay'
        },
        dayHeaderFormat: { weekday: 'long' },
        events: calendarEvents,
        eventClick: function(info) {
            const props = info.event.extendedProps;
            const eventDate = info.event.start ? new Date(info.event.start).toISOString().split('T')[0] : '';
            
            // Formu doldur
            document.getElementById('class_id').value = props.class_id || '';
            document.getElementById('original_date').value = eventDate;
            
            // Seçim modal'ı göster
            showEventSelectionModal(props.full_class_name || info.event.title, props.class_id, eventDate);
        }
    });
    
    calendar.render();
});

function showEventSelectionModal(className, classId, eventDate) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center';
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">${className}</h3>
            <p class="text-sm text-gray-600 mb-4">Bu dersi iptal mi etmek yoksa ertelemek mi istiyorsunuz?</p>
            <div class="flex gap-3">
                <button onclick="selectCancellationType('cancelled', '${classId}', '${eventDate}')" 
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                    <i class="fas fa-times mr-2"></i>İptal Et
                </button>
                <button onclick="selectCancellationType('postponed', '${classId}', '${eventDate}')" 
                        class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 font-medium">
                    <i class="fas fa-calendar-alt mr-2"></i>Ertele
                </button>
                <button onclick="closeSelectionModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium">
                    İptal
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    window.selectCancellationType = function(type, classId, eventDate) {
        document.getElementById('class_id').value = classId;
        document.getElementById('cancellation_type').value = type;
        document.getElementById('original_date').value = eventDate;
        
        if (type === 'postponed') {
            document.getElementById('new_date').focus();
        }
        
        closeSelectionModal();
    };
    
    window.closeSelectionModal = function() {
        modal.remove();
    };
    
    modal.onclick = function(e) {
        if (e.target === modal) {
            closeSelectionModal();
        }
    };
}
</script>
@endpush
@endsection
