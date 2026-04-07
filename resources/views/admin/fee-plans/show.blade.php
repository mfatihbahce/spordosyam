@extends('layouts.panel')

@section('title', 'Aidat Planı Detayı')
@section('page-title', $feePlan->name)

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.fee-plans.index') }}" class="text-indigo-600 hover:text-indigo-900">← Geri Dön</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Plan Bilgileri</h3>
        <dl class="space-y-3">
            <div>
                <dt class="text-sm font-medium text-gray-500">Plan Adı</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $feePlan->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Tutar (Aylık)</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ number_format($feePlan->amount, 2) }} ₺</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Durum</dt>
                <dd class="mt-1">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $feePlan->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $feePlan->is_active ? 'Aktif' : 'Pasif' }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">İstatistikler</h3>
        <dl class="space-y-3">
            <div>
                <dt class="text-sm font-medium text-gray-500">Toplam Öğrenci</dt>
                <dd class="mt-1 text-2xl font-bold text-gray-900">{{ $feePlan->student_fees_count }}</dd>
            </div>
        </dl>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">Öğrenci Aidatları</h3>
    @if($feePlan->studentFees->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Öğrenci</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vade Tarihi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($feePlan->studentFees as $studentFee)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $studentFee->student->first_name }} {{ $studentFee->student->last_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($studentFee->amount, 2) }} ₺</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $studentFee->due_date->format('d.m.Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'paid' => 'bg-green-100 text-green-800',
                                    'overdue' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                ];
                                $statusLabels = [
                                    'pending' => 'Beklemede',
                                    'paid' => 'Ödendi',
                                    'overdue' => 'Gecikmiş',
                                    'cancelled' => 'İptal',
                                ];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$studentFee->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$studentFee->status] ?? $studentFee->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500">Bu plana henüz öğrenci atanmamış.</p>
    @endif
</div>
@endsection
