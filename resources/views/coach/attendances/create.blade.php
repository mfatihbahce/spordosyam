@extends('layouts.panel')

@section('title', 'Yoklama Al')
@section('page-title', 'Yoklama Al')

@section('sidebar-menu')
@include('coach.partials.sidebar')
@endsection

@section('content')
@if(!isset($formMode) || !$formMode)
{{-- Bugünkü dersler kart listesi --}}
<div class="mb-6 p-4 rounded-xl bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-100">
    <div class="flex items-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-indigo-600">
            <i class="fas fa-calendar-day text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Bugünkü Dersler</h2>
            <p class="text-sm text-gray-600 mt-0.5">{{ now()->format('d.m.Y') }} — Ders saatinden 15 dakika önce yoklama alabilirsiniz.</p>
        </div>
    </div>
</div>

@if(count($todayCards ?? []) > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @foreach($todayCards as $card)
    @php
        $isMakeup = $card['type'] === 'makeup';
        $canTake = $card['can_take'];
        $cardUrl = $isMakeup ? route('coach.attendances.create', ['makeup_session_id' => $card['id'], 'date' => $today]) : route('coach.attendances.create', ['class_id' => $card['id'], 'date' => $today]);
    @endphp
    <div class="relative overflow-hidden rounded-xl border-2 transition-all duration-200
                {{ $canTake
                    ? 'border-indigo-200 bg-white shadow-md hover:shadow-lg hover:border-indigo-400 cursor-pointer hover:-translate-y-0.5'
                    : 'border-gray-200 bg-gray-50/80' }}"
         @if($canTake)
         onclick="window.location='{{ $cardUrl }}';"
         @else
         onclick="alert('Henüz ders saati yaklaşmadı. En az 15 dakika öncesinde yoklama alabilirsiniz.\n\nYoklama alınabilecek saat: {{ $card['opens_at'] }}');"
         @endif
    >
        {{-- Sol kenar renk şeridi --}}
        <div class="absolute left-0 top-0 bottom-0 w-1 {{ $isMakeup ? 'bg-gradient-to-b from-purple-500 to-purple-600' : 'bg-gradient-to-b from-indigo-500 to-indigo-600' }}"></div>

        <div class="pl-5 pr-5 py-5">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                    @if($isMakeup)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700 mb-3">
                        <i class="fas fa-calendar-plus text-purple-500"></i> Telafi
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-700 mb-3">
                        <i class="fas fa-book text-indigo-500"></i> Ders
                    </span>
                    @endif
                    <h3 class="font-bold text-gray-900 text-base leading-tight truncate" title="{{ $card['name'] }}">{{ $card['name'] }}</h3>
                    <div class="flex items-center gap-2 mt-2 text-sm text-gray-600">
                        <i class="fas fa-clock text-gray-400 w-4"></i>
                        <span>{{ $card['start_time'] }} – {{ $card['end_time'] ?? '-' }}</span>
                    </div>
                    @if($canTake)
                    <div class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-green-600">
                        <i class="fas fa-check-circle"></i>
                        <span>Yoklama almak için tıklayın</span>
                    </div>
                    @else
                    <div class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-amber-600">
                        <i class="fas fa-hourglass-half"></i>
                        <span>Yoklama açılış: {{ $card['opens_at'] }}</span>
                    </div>
                    @endif
                </div>
                @if($canTake)
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 group-hover:bg-indigo-200 transition-colors">
                    <i class="fas fa-chevron-right text-sm"></i>
                </div>
                @else
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                    <i class="fas fa-lock text-sm"></i>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="bg-white rounded-lg shadow p-8 text-center">
    <p class="text-gray-500">Bugün için planlanmış dersiniz bulunmuyor.</p>
</div>
@endif

@else
{{-- Yoklama formu (ders seçildikten sonra) --}}
<div class="mb-4">
    <a href="{{ route('coach.attendances.create') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>
        Bugünkü derslere dön
    </a>
</div>

@if(empty($formStudents))
<div class="bg-amber-50 border border-amber-200 rounded-lg p-6 text-center">
    <p class="text-amber-800">Bu ders için öğrenci bulunamadı.</p>
    <a href="{{ route('coach.attendances.create') }}" class="inline-block mt-4 text-indigo-600 hover:text-indigo-900">Bugünkü derslere dön</a>
</div>
@else
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('coach.attendances.store') }}" method="POST" id="attendanceForm">
        @csrf
        <input type="hidden" name="class_id" id="class_id" value="{{ $preselectedClassId ?? '' }}">
        <input type="hidden" name="makeup_session_id" id="makeup_session_id" value="{{ $preselectedMakeupSessionId ?? '' }}">
        <input type="hidden" name="attendance_date" id="attendance_date" value="{{ $preselectedDate ?? $today }}">
        <input type="hidden" name="attendance_time" id="attendance_time" value="{{ old('attendance_time', date('H:i')) }}">

        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <p class="text-sm font-medium text-gray-700">
                @if($preselectedClassId)
                    Sınıf: {{ $classes->firstWhere('id', $preselectedClassId)->name ?? '-' }}
                @elseif($preselectedMakeupSessionId)
                    Telafi dersi: {{ $makeupSessions->firstWhere('id', $preselectedMakeupSessionId)->name ?? 'Telafi Dersi' }}
                @endif
                — {{ \Carbon\Carbon::parse($preselectedDate)->format('d.m.Y') }}
            </p>
        </div>

        <div id="studentsContainer">
            <h3 class="text-lg font-semibold mb-4">Öğrenci Yoklamaları</h3>
            <div class="mb-4 flex space-x-2">
                <button type="button" onclick="markAllPresent()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    Tümünü Mevcut Yap
                </button>
                <button type="button" onclick="markAllAbsent()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                    Tümünü Yok Yap
                </button>
            </div>
            @if($formIsMakeup ?? false)
            <p class="text-sm text-purple-600 mb-2">Telafi dersi yoklaması — sadece Var/Yok işaretlenir.</p>
            @endif
            <div id="studentsList" class="space-y-3">
                @foreach($formStudents ?? [] as $student)
                <div class="flex items-center justify-between border rounded-lg p-4">
                    <div class="flex-1">
                        <p class="font-medium">{{ $student->first_name }} {{ $student->last_name }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="attendances[{{ $student->id }}][status]" value="present" checked class="mr-2">
                            <span class="text-sm">Var</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="attendances[{{ $student->id }}][status]" value="absent" class="mr-2">
                            <span class="text-sm">Yok</span>
                        </label>
                        @if(!($formIsMakeup ?? false) && ($makeupClassEnabled ?? false))
                        <label class="flex items-center">
                            <input type="radio" name="attendances[{{ $student->id }}][status]" value="excused" class="mr-2">
                            <span class="text-sm">İzinli</span>
                        </label>
                        @endif
                        <input type="hidden" name="attendances[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                        <input type="text" name="attendances[{{ $student->id }}][notes]" placeholder="Not" class="text-sm border rounded px-2 py-1 w-32">
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('coach.attendances.create') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                İptal
            </a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700" id="submitBtn">
                Kaydet
            </button>
        </div>
    </form>
</div>
@endif
@endif

@push('scripts')
<script>
const classes = @json($classes->keyBy('id'));
const makeupSessions = @json($makeupSessionsForJs ?? []);
const makeupClassEnabled = @json($makeupClassEnabled ?? false);

function loadStudents() {
    const sel = document.getElementById('class_or_makeup');
    const val = sel.value;
    const classIdInput = document.getElementById('class_id');
    const makeupIdInput = document.getElementById('makeup_session_id');
    const container = document.getElementById('studentsContainer');
    const list = document.getElementById('studentsList');
    const submitBtn = document.getElementById('submitBtn');
    const dateInput = document.getElementById('attendance_date');
    const timeInput = document.getElementById('attendance_time');
    const makeupNote = document.getElementById('makeupNote');

    classIdInput.value = '';
    makeupIdInput.value = '';

    if (!val) {
        container.classList.add('hidden');
        makeupNote.classList.add('hidden');
        submitBtn.disabled = true;
        return;
    }

    const isMakeup = val.startsWith('m_');
    const id = val.replace('c_', '').replace('m_', '');

    if (isMakeup) {
        makeupIdInput.value = id;
        const session = makeupSessions[id];
        if (!session || !session.students || session.students.length === 0) {
            list.innerHTML = '<p class="text-gray-500">Bu telafi dersinde öğrenci bulunmamaktadır.</p>';
            container.classList.remove('hidden');
            makeupNote.classList.remove('hidden');
            submitBtn.disabled = true;
            return;
        }
        dateInput.value = session.scheduled_date;
        timeInput.value = session.start_time || timeInput.value;
        dateInput.readOnly = true;
        timeInput.readOnly = true;
        makeupNote.classList.remove('hidden');
        renderStudents(session.students, true);
    } else {
        classIdInput.value = id;
        makeupIdInput.value = '';
        dateInput.readOnly = false;
        timeInput.readOnly = false;
        makeupNote.classList.add('hidden');
        const selectedClass = classes[id];
        if (!selectedClass || !selectedClass.students || selectedClass.students.length === 0) {
            list.innerHTML = '<p class="text-gray-500">Bu sınıfta öğrenci bulunmamaktadır.</p>';
            container.classList.remove('hidden');
            submitBtn.disabled = true;
            return;
        }
        renderStudents(selectedClass.students, false);
    }

    container.classList.remove('hidden');
    submitBtn.disabled = false;
}

function renderStudents(students, isMakeup) {
    const list = document.getElementById('studentsList');
    list.innerHTML = '';
    students.forEach(student => {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between border rounded-lg p-4';
        div.innerHTML = `
            <div class="flex-1">
                <p class="font-medium">${student.first_name} ${student.last_name}</p>
            </div>
            <div class="flex items-center space-x-4">
                <label class="flex items-center">
                    <input type="radio" name="attendances[${student.id}][status]" value="present" checked class="mr-2">
                    <span class="text-sm">Var</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="attendances[${student.id}][status]" value="absent" class="mr-2">
                    <span class="text-sm">Yok</span>
                </label>
                ${!isMakeup && makeupClassEnabled ? `
                <label class="flex items-center">
                    <input type="radio" name="attendances[${student.id}][status]" value="excused" class="mr-2">
                    <span class="text-sm">İzinli</span>
                </label>
                ` : ''}
                <input type="hidden" name="attendances[${student.id}][student_id]" value="${student.id}">
                <input type="text" name="attendances[${student.id}][notes]" placeholder="Not" class="text-sm border rounded px-2 py-1 w-32">
            </div>
        `;
        list.appendChild(div);
    });
}

function markAllPresent() {
    document.querySelectorAll('input[value="present"]').forEach(radio => radio.checked = true);
}

function markAllAbsent() {
    document.querySelectorAll('input[value="absent"]').forEach(radio => radio.checked = true);
}
</script>
@endpush
@endsection
