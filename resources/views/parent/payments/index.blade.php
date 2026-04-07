@extends('layouts.panel')

@section('title', 'Aidatlarım')
@section('page-title', 'Aidatlarım')

@section('sidebar-menu')
@include('parent.partials.sidebar')
@endsection

@section('content')
<div class="mb-4">
    <h3 class="text-lg font-semibold">Ödenmemiş Aidatlar</h3>
</div>

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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aidat</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutar</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vade Tarihi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($studentFees as $studentFee)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $studentFee->student->first_name }} {{ $studentFee->student->last_name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $studentFee->fee_label }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-semibold">{{ number_format($studentFee->amount, 2) }} ₺</td>
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
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    @if($studentFee->status !== 'paid')
                        <a href="{{ route('parent.payments.create', $studentFee) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                            Öde
                        </a>
                    @else
                        <span class="text-gray-400">Ödendi</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Ödenmemiş aidat bulunmamaktadır.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $studentFees->links() }}
    </div>
</div>
@endsection
