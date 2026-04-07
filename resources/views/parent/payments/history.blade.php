@extends('layouts.panel')

@section('title', 'Ödeme Geçmişi')
@section('page-title', 'Ödeme Geçmişi')

@section('sidebar-menu')
@include('parent.partials.sidebar')
@endsection

@section('content')
<div class="mb-4">
    <h3 class="text-lg font-semibold">Ödeme Geçmişi</h3>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Öğrenci</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aidat Planı</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutar</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ödeme Yöntemi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($payments as $payment)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $payment->studentFee->student->first_name }} {{ $payment->studentFee->student->last_name }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->studentFee->fee_label }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-semibold">{{ number_format($payment->amount, 2) }} ₺</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    @if($payment->payment_method === 'credit_card')
                        Kredi Kartı
                    @elseif($payment->payment_method === 'bank_transfer')
                        Havale/EFT
                    @else
                        Nakit
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'failed' => 'bg-red-100 text-red-800',
                            'refunded' => 'bg-gray-100 text-gray-800',
                        ];
                        $statusLabels = [
                            'pending' => 'Beklemede',
                            'completed' => 'Tamamlandı',
                            'failed' => 'Başarısız',
                            'refunded' => 'İade Edildi',
                        ];
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$payment->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $statusLabels[$payment->status] ?? $payment->status }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    @if($payment->paid_at)
                        {{ $payment->paid_at->format('d.m.Y H:i') }}
                    @else
                        {{ $payment->created_at->format('d.m.Y H:i') }}
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Henüz ödeme geçmişi bulunmamaktadır.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $payments->links() }}
    </div>
</div>
@endsection
