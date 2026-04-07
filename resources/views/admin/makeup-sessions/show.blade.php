@extends('layouts.panel')

@section('title', 'Telafi Dersi Detay')
@section('page-title', 'Telafi Dersi Detay')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
@php
    $startTimeStr = $makeupSession->start_time instanceof \DateTimeInterface ? $makeupSession->start_time->format('H:i') : \Carbon\Carbon::parse($makeupSession->start_time)->format('H:i');
    $sessionStart = \Carbon\Carbon::parse($makeupSession->scheduled_date->format('Y-m-d') . ' ' . $startTimeStr);
    $hasStarted = now()->gte($sessionStart);
@endphp
<div class="mb-4 flex justify-between items-center">
    <a href="{{ route('admin.makeup-sessions.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>
        Geri Dön
    </a>
    @if(!$hasStarted)
    <div class="space-x-2">
        <button type="button" onclick="openAddStudentsModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
            <i class="fas fa-user-plus mr-2"></i>Öğrenci Ekle
        </button>
        <a href="{{ route('admin.makeup-sessions.edit', $makeupSession) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            <i class="fas fa-edit mr-2"></i>Düzenle
        </a>
    </div>
    @endif
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-white/20 text-white mr-2">Telafi Dersi</span>
        <h2 class="text-xl font-bold text-white mt-2">{{ $makeupSession->name ?? 'Telafi Dersi' }}</h2>
        <p class="text-purple-100 text-sm mt-1">
            {{ $makeupSession->scheduled_date->format('d.m.Y') }}
            {{ \Carbon\Carbon::parse($makeupSession->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($makeupSession->end_time)->format('H:i') }}
            · Antrenör: {{ $makeupSession->coach->user->name ?? '-' }}
            @if($makeupSession->branch)
                · {{ $makeupSession->branch->name }}
            @endif
        </p>
    </div>

    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Bu derse eklenen öğrenciler</h3>
        @if($makeupSession->studentMakeupClasses->count() > 0)
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Öğrenci</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Orijinal sınıf</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($makeupSession->studentMakeupClasses as $sm)
                <tr>
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">
                        {{ $sm->student->first_name }} {{ $sm->student->last_name }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">
                        {{ $sm->makeupClass?->originalClass?->name ?? '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-gray-500 text-sm">Henüz öğrenci eklenmemiş. "Öğrenci Ekle" ile telafi bekleyen öğrencilerden seçin.</p>
        @endif
    </div>
</div>

<!-- Öğrenci Ekle Modal -->
<div id="addStudentsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Telafi bekleyen öğrencilerden ekle</h3>
            <button type="button" onclick="closeAddStudentsModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('admin.makeup-sessions.add-students', $makeupSession) }}" method="POST" id="addStudentsForm">
            @csrf
            <div id="pendingStudentsList" class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-3 mb-4">
                <p class="text-gray-500 text-sm">Yükleniyor...</p>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeAddStudentsModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">İptal</button>
                <button type="submit" id="addStudentsSubmit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700" disabled>
                    Seçilenleri Ekle
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openAddStudentsModal() {
    document.getElementById('addStudentsModal').classList.remove('hidden');
    loadPendingStudents();
}

function closeAddStudentsModal() {
    document.getElementById('addStudentsModal').classList.add('hidden');
}

function loadPendingStudents() {
    const container = document.getElementById('pendingStudentsList');
    container.innerHTML = '<p class="text-gray-500 text-sm">Yükleniyor...</p>';
    fetch('{{ route("admin.makeup-sessions.pending-students") }}')
        .then(res => res.json())
        .then(data => {
            if (!data.students || data.students.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-sm">Telafi bekleyen öğrenci yok.</p>';
                return;
            }
            let html = '';
            data.students.forEach(s => {
                html += `<label class="flex items-center py-2 hover:bg-gray-50 rounded px-2 cursor-pointer">
                    <input type="checkbox" name="student_makeup_ids[]" value="${s.id}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-3 pending-check">
                    <span class="text-sm font-medium text-gray-900">${s.student_name}</span>
                    <span class="text-xs text-gray-500 ml-2">(${s.original_class})</span>
                </label>`;
            });
            container.innerHTML = html;
            document.querySelectorAll('.pending-check').forEach(cb => {
                cb.addEventListener('change', toggleAddSubmit);
            });
            toggleAddSubmit();
        })
        .catch(() => {
            container.innerHTML = '<p class="text-red-500 text-sm">Yüklenemedi.</p>';
        });
}

function toggleAddSubmit() {
    const any = document.querySelector('.pending-check:checked');
    document.getElementById('addStudentsSubmit').disabled = !any;
}

window.onclick = function(event) {
    const modal = document.getElementById('addStudentsModal');
    if (event.target === modal) closeAddStudentsModal();
};
</script>
@endpush
@endsection
