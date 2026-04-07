@extends('layouts.panel')

@section('title', 'Yeni Öğrenci Aidatı Ekle')
@section('page-title', 'Yeni Öğrenci Aidatı Ekle')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
@if(session('error'))
    <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.student-fees.store') }}" method="POST">
        @csrf
        
        <div class="mb-4 p-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm">
            Her ayın seçtiğiniz günü için başlangıç–bitiş tarihi aralığında otomatik aidat oluşturulur. Öğrenci aktif olmalıdır.
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="student_id" class="block text-sm font-medium text-gray-700">Öğrenci *</label>
                <select name="student_id" id="student_id" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border">
                    <option value="">Öğrenci Seçiniz</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->first_name }} {{ $student->last_name }}
                        </option>
                    @endforeach
                </select>
                @error('student_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="fee_plan_id" class="block text-sm font-medium text-gray-700">Aidat Planı *</label>
                <select name="fee_plan_id" id="fee_plan_id" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border"
                        onchange="updateAmount()">
                    <option value="">Aidat Planı Seçiniz</option>
                    @foreach($feePlans as $feePlan)
                        <option value="{{ $feePlan->id }}" 
                                data-amount="{{ $feePlan->amount }}"
                                {{ old('fee_plan_id') == $feePlan->id ? 'selected' : '' }}>
                            {{ $feePlan->name }} ({{ number_format($feePlan->amount, 2) }} ₺/ay)
                        </option>
                    @endforeach
                </select>
                @error('fee_plan_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="day_of_month" class="block text-sm font-medium text-gray-700">Her ayın kaçı *</label>
                <input type="number" name="day_of_month" id="day_of_month" required min="1" max="31"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border"
                       value="{{ old('day_of_month', 15) }}" placeholder="1-31">
                <p class="mt-1 text-xs text-gray-500">Veli her ayın bu günü beklemede aidat görecek (örn. 15 → 15 Şubat, 15 Mart…)</p>
                @error('day_of_month')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700">Tutar (₺)</label>
                <input type="number" name="amount" id="amount" min="0" step="0.01"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border"
                       value="{{ old('amount') }}" placeholder="Boş bırakılırsa plan tutarı kullanılır">
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Başlangıç tarihi *</label>
                <input type="date" name="start_date" id="start_date" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border"
                       value="{{ old('start_date') }}">
                <p class="mt-1 text-xs text-gray-500">Dönem başlangıcı (ilk vade bu tarihten sonraki ilgili günde)</p>
                @error('start_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">Bitiş tarihi *</label>
                <input type="date" name="end_date" id="end_date" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border"
                       value="{{ old('end_date') }}">
                <p class="mt-1 text-xs text-gray-500">Öğrencinin dersi bittiği / aidatın bittiği tarih (örn. 17 Nisan)</p>
                @error('end_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-gray-700">Notlar</label>
                <textarea name="notes" id="notes" rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.student-fees.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                İptal
            </a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                Kaydet
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function updateAmount() {
    const select = document.getElementById('fee_plan_id');
    const amountInput = document.getElementById('amount');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value && !amountInput.value) {
        amountInput.value = selectedOption.getAttribute('data-amount');
    }
}
</script>
@endpush
@endsection
