@extends('layouts.panel')

@section('title', 'Öğrenci Aidatı Düzenle')
@section('page-title', 'Öğrenci Aidatı Düzenle')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.student-fees.update', $studentFee) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="student_id" class="block text-sm font-medium text-gray-700">Öğrenci *</label>
                <select name="student_id" id="student_id" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border">
                    <option value="">Öğrenci Seçiniz</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('student_id', $studentFee->student_id) == $student->id ? 'selected' : '' }}>
                            {{ $student->first_name }} {{ $student->last_name }}
                        </option>
                    @endforeach
                </select>
                @error('student_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                @if($studentFee->fee_plan_id)
                <label for="fee_plan_id" class="block text-sm font-medium text-gray-700">Aidat Planı *</label>
                <select name="fee_plan_id" id="fee_plan_id" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border">
                    <option value="">Aidat Planı Seçiniz</option>
                    @foreach($feePlans as $feePlan)
                        <option value="{{ $feePlan->id }}" {{ old('fee_plan_id', $studentFee->fee_plan_id) == $feePlan->id ? 'selected' : '' }}>
                            {{ $feePlan->name }} ({{ number_format($feePlan->amount, 2) }} ₺)
                        </option>
                    @endforeach
                </select>
                @error('fee_plan_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @else
                <label class="block text-sm font-medium text-gray-700">Aidat</label>
                <p class="mt-1 text-sm text-gray-500">Manuel tanımlı aidat (öğrenci detay sayfasından eklenen)</p>
                @endif
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700">Tutar (₺) *</label>
                <input type="number" name="amount" id="amount" required min="0" step="0.01"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border"
                       value="{{ old('amount', $studentFee->amount) }}">
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="due_date" class="block text-sm font-medium text-gray-700">Vade Tarihi *</label>
                <input type="date" name="due_date" id="due_date" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border"
                       value="{{ old('due_date', $studentFee->due_date->format('Y-m-d')) }}">
                @error('due_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Durum *</label>
                <select name="status" id="status" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border">
                    <option value="pending" {{ old('status', $studentFee->status) == 'pending' ? 'selected' : '' }}>Beklemede</option>
                    <option value="paid" {{ old('status', $studentFee->status) == 'paid' ? 'selected' : '' }}>Ödendi</option>
                    <option value="overdue" {{ old('status', $studentFee->status) == 'overdue' ? 'selected' : '' }}>Gecikmiş</option>
                    <option value="cancelled" {{ old('status', $studentFee->status) == 'cancelled' ? 'selected' : '' }}>İptal</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-gray-700">Notlar</label>
                <textarea name="notes" id="notes" rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border">{{ old('notes', $studentFee->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.student-fees.show', $studentFee) }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                İptal
            </a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                Güncelle
            </button>
        </div>
    </form>
</div>
@endsection
