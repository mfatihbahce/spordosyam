@extends('layouts.panel')

@section('title', 'Telafi Bekleyen Öğrenciler')
@section('page-title', 'Telafi Bekleyen Öğrenciler')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4 flex justify-between items-center">
    <div>
        <h3 class="text-lg font-semibold">Telafi Dersi Bekleyen Öğrenciler</h3>
        <p class="text-sm text-gray-500 mt-1">İzinli öğrencilere telafi hakkı tanımak için tarih seçin ve ilerideki uygun bir derse ekleyin.</p>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Öğrenci</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sınıf</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Yoklama Tarihi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                {{-- İşlemler kolonu şimdilik gizli --}}
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($studentMakeupClasses as $studentMakeup)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $studentMakeup->student->first_name }} {{ $studentMakeup->student->last_name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $studentMakeup->makeupClass->originalClass->name ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $studentMakeup->attendance ? $studentMakeup->attendance->attendance_date->format('d.m.Y') : '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        Bekliyor
                    </span>
                </td>
                {{-- İşlemler kolonu şimdilik gizli
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button onclick="openScheduleModal({{ $studentMakeup->id }})" class="text-indigo-600 hover:text-indigo-900">
                        Derse Ekle
                    </button>
                </td>
                --}}
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                    Telafi bekleyen öğrenci bulunmamaktadır.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($studentMakeupClasses->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $studentMakeupClasses->links() }}
    </div>
    @endif
</div>

<!-- Modal -->
<div id="scheduleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Öğrenciyi İlerideki Bir Derse Telafi Olarak Ekle</h3>
            <p class="text-sm text-gray-500 mb-4">Tarih seçin; o gün için listeden uygun dersi seçebilirsiniz.</p>
            <form id="scheduleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Telafi Tarihi *</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                           min="{{ date('Y-m-d') }}">
                </div>
                <div class="mb-4">
                    <label for="scheduled_class_id" class="block text-sm font-medium text-gray-700 mb-2">Ders</label>
                    <select name="scheduled_class_id" id="scheduled_class_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Önce tarih seçin</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Seçilen tarihte yapılacak dersi seçin; öğrenci bu derse telafi olarak eklenecektir.</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeScheduleModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        İptal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentStudentMakeupId = null;

function openScheduleModal(studentMakeupId) {
    currentStudentMakeupId = studentMakeupId;
    const form = document.getElementById('scheduleForm');
    form.action = `/admin/student-makeup-classes/${studentMakeupId}`;
    document.getElementById('scheduleModal').classList.remove('hidden');
}

function closeScheduleModal() {
    document.getElementById('scheduleModal').classList.add('hidden');
    currentStudentMakeupId = null;
}

// Tarih değiştiğinde o gün için dersleri getir
document.getElementById('scheduled_date').addEventListener('change', function() {
    const date = this.value;
    if (!date) return;
    
    fetch(`/admin/student-makeup-classes/classes-by-date?date=${date}`)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('scheduled_class_id');
            select.innerHTML = '<option value="">Yeni Tarih Seç</option>';
            data.classes.forEach(cls => {
                const option = document.createElement('option');
                option.value = cls.id;
                option.textContent = `${cls.name} - ${cls.coach} (${cls.time})`;
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Error:', error));
});

// Modal dışına tıklandığında kapat
window.onclick = function(event) {
    const modal = document.getElementById('scheduleModal');
    if (event.target == modal) {
        closeScheduleModal();
    }
}
</script>
@endpush
@endsection
