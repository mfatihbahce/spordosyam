@extends('layouts.panel')

@section('title', 'Telafi Dersi Tarihi Belirle')
@section('page-title', 'Telafi Dersi Tarihi Belirle')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.class-cancellations.show', $classCancellation) }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>
        Geri Dön
    </a>
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
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
        <h2 class="text-xl font-bold text-white">Telafi Dersi Tarihi Belirle</h2>
    </div>

    <!-- Form -->
    <div class="p-6">
        <!-- Mevcut Bilgiler -->
        <div class="mb-6 bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Mevcut Bilgiler</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-sm font-medium text-gray-500">Sınıf:</span>
                    <p class="text-gray-900 font-semibold">{{ $classCancellation->classModel->name }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Orijinal Tarih:</span>
                    <p class="text-gray-900 font-semibold">{{ $classCancellation->original_date->format('d.m.Y') }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Tip:</span>
                    <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full {{ $classCancellation->cancellation_type === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $classCancellation->cancellation_type === 'cancelled' ? 'İptal' : 'Erteleme' }}
                    </span>
                </div>
                @if($classCancellation->reason)
                <div class="md:col-span-2">
                    <span class="text-sm font-medium text-gray-500">Neden:</span>
                    <p class="text-gray-700">{{ $classCancellation->reason }}</p>
                </div>
                @endif
            </div>
        </div>

        <form action="{{ route('admin.class-cancellations.update', $classCancellation) }}" method="POST" id="scheduleForm">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="new_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Yeni Tarih *
                    </label>
                    <input type="date" name="new_date" id="new_date" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('new_date', $classCancellation->new_date ? $classCancellation->new_date->format('Y-m-d') : '') }}"
                           min="{{ date('Y-m-d') }}">
                    <p class="mt-1 text-xs text-gray-500">Telafi dersinin yapılacağı tarihi seçiniz</p>
                    @error('new_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="scheduled_class_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Var Olan Ders (Opsiyonel)
                    </label>
                    <select name="scheduled_class_id" id="scheduled_class_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Yeni tarih/saat belirle</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('scheduled_class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Var olan bir derse telafi olarak eklemek için seçiniz. Seçmezseniz aşağıda saat girin; seçilen saat başka dersle çakışmamalıdır.</p>
                    @error('scheduled_class_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="timeFields" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Başlangıç saati</label>
                        <input type="time" name="start_time" id="start_time"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                               value="{{ old('start_time') }}">
                        <p class="mt-1 text-xs text-gray-500">Var olan ders seçmediyseniz telafi saati girin; çakışma kontrolü yapılır.</p>
                        @error('start_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">Bitiş saati</label>
                        <input type="time" name="end_time" id="end_time"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                               value="{{ old('end_time') }}">
                        @error('end_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.class-cancellations.show', $classCancellation) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    İptal
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-save mr-2"></i>
                    Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Tarih değiştiğinde o gün için dersleri getir
document.getElementById('new_date').addEventListener('change', function() {
    const date = this.value;
    if (!date) return;
    
    fetch(`{{ route('admin.student-makeup-classes.classes-by-date') }}?date=${date}`)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('scheduled_class_id');
            const currentValue = select.value;
            select.innerHTML = '<option value="">Yeni Tarih Seç</option>';
            
            if (data.classes && data.classes.length > 0) {
                data.classes.forEach(cls => {
                    const option = document.createElement('option');
                    option.value = cls.id;
                    option.textContent = `${cls.name} - ${cls.coach} (${cls.time})`;
                    if (cls.id == currentValue) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
});

// Form submit edilirken loading göster
document.getElementById('scheduleForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Kaydediliyor...';
});
</script>
@endpush
@endsection
