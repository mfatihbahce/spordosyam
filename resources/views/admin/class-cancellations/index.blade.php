@extends('layouts.panel')

@section('title', 'İptal Olan Dersler')
@section('page-title', 'İptal Olan Dersler')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')


@if(isset($makeupEnabled) && $makeupEnabled)
<div class="mb-4 p-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm">
    <i class="fas fa-info-circle mr-2"></i>
    Bekleyen öğrenci sayısı 0 olan dersler listede gösterilmez.
</div>
@else
<div class="mb-4 p-4 rounded-lg bg-gray-100 border border-gray-200 text-gray-700 text-sm">
    <i class="fas fa-info-circle mr-2"></i>
    Telafi dersi kapalı. İptal kaydedilen derslerde öğrencilerin ders hakkından 1 düşülür; telafi eklenmez.
</div>
@endif

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sınıf</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tip</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orijinal Tarih</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Yeni Tarih</th>
                @if(isset($makeupEnabled) && $makeupEnabled)
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telafi Bekleyen</th>
                @endif
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Neden</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($cancellations as $cancellation)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $cancellation->classModel->name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $cancellation->cancellation_type === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $cancellation->cancellation_type === 'cancelled' ? 'İptal' : 'Erteleme' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $cancellation->original_date->format('d.m.Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $cancellation->new_date ? $cancellation->new_date->format('d.m.Y') : '-' }}
                </td>
                @if(isset($makeupEnabled) && $makeupEnabled)
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                    {{ $cancellation->pending_students_count ?? 0 }}
                </td>
                @endif
                <td class="px-6 py-4 text-sm text-gray-500">
                    {{ $cancellation->reason ? \Illuminate\Support\Str::limit($cancellation->reason, 50) : '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($cancellation->status === 'scheduled')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Planlandı
                    </span>
                    @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        Bekliyor
                    </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                    @if(isset($makeupEnabled) && $makeupEnabled)
                    @if(($cancellation->pending_students_count ?? 0) > 0)
                    <button type="button" class="detail-waiting-btn text-indigo-600 hover:text-indigo-900 font-medium" data-url="{{ route('admin.class-cancellations.waiting-students', $cancellation) }}" data-class-name="{{ $cancellation->classModel->name ?? '' }}">Detay</button>
                    @endif
                    @if($cancellation->status === 'pending')
                    <a href="{{ route('admin.class-cancellations.add-makeup', $cancellation) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                        Telafi Ekle
                    </a>
                    @endif
                    @else
                    <span class="text-gray-500 text-xs">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ (isset($makeupEnabled) && $makeupEnabled) ? 8 : 7 }}" class="px-6 py-4 text-center text-sm text-gray-500">
                    {{ (isset($makeupEnabled) && $makeupEnabled) ? 'Bekleyen telafi dersi bulunmamaktadır.' : 'İptal kaydı bulunmamaktadır.' }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($cancellations->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $cancellations->links() }}
    </div>
    @endif
</div>

{{-- Detay popup: sadece bekleyen öğrenciler --}}
<div id="waitingStudentsModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" id="waitingStudentsModalBackdrop"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[80vh] flex flex-col" role="dialog" aria-labelledby="waitingStudentsModalTitle" aria-modal="true">
            <div class="p-5 border-b border-gray-200">
                <h3 id="waitingStudentsModalTitle" class="text-lg font-bold text-gray-900">Bekleyen Öğrenciler</h3>
                <p id="waitingStudentsModalClassName" class="text-sm text-gray-500 mt-0.5"></p>
            </div>
            <div class="p-5 overflow-y-auto flex-1">
                <div id="waitingStudentsModalLoading" class="text-center py-8 text-gray-500">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <p>Yükleniyor...</p>
                </div>
                <ul id="waitingStudentsModalList" class="divide-y divide-gray-100 hidden"></ul>
                <p id="waitingStudentsModalEmpty" class="text-sm text-gray-500 hidden">Bekleyen öğrenci bulunmuyor.</p>
            </div>
            <div class="p-5 border-t border-gray-200 flex justify-end">
                <button type="button" id="waitingStudentsModalClose" class="px-4 py-2 rounded-xl bg-gray-200 text-gray-700 font-medium hover:bg-gray-300 transition-colors">Kapat</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const modal = document.getElementById('waitingStudentsModal');
    const backdrop = document.getElementById('waitingStudentsModalBackdrop');
    const title = document.getElementById('waitingStudentsModalClassName');
    const loading = document.getElementById('waitingStudentsModalLoading');
    const listEl = document.getElementById('waitingStudentsModalList');
    const emptyEl = document.getElementById('waitingStudentsModalEmpty');
    const closeBtn = document.getElementById('waitingStudentsModalClose');

    function openModal() {
        modal.classList.remove('hidden');
    }
    function closeModal() {
        modal.classList.add('hidden');
    }

    closeBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);

    document.querySelectorAll('.detail-waiting-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var url = this.getAttribute('data-url');
            var className = this.getAttribute('data-class-name') || '';
            title.textContent = className;
            listEl.classList.add('hidden');
            listEl.innerHTML = '';
            emptyEl.textContent = 'Bekleyen öğrenci bulunmuyor.';
            emptyEl.classList.add('hidden');
            loading.classList.remove('hidden');
            openModal();

            fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    loading.classList.add('hidden');
                    var students = data.students || [];
                    if (students.length === 0) {
                        emptyEl.classList.remove('hidden');
                    } else {
                        students.forEach(function(s) {
                            var li = document.createElement('li');
                            li.className = 'py-3 flex items-start gap-3';
                            var parentInfo = '<div class="mt-1 text-xs text-gray-500">Veli: ' + (s.parent_name || '-') + ' · Tel: ' + (s.parent_phone || '-') + '</div>';
                            li.innerHTML = '<span class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-semibold">' + (s.name ? s.name.charAt(0).toUpperCase() : '-') + '</span><div><span class="text-gray-900 font-medium">' + (s.name || '-') + '</span>' + parentInfo + '</div>';
                            listEl.appendChild(li);
                        });
                        listEl.classList.remove('hidden');
                    }
                })
                .catch(function() {
                    loading.classList.add('hidden');
                    emptyEl.textContent = 'Liste yüklenemedi.';
                    emptyEl.classList.remove('hidden');
                });
        });
    });
})();
</script>
@endpush
@endsection
