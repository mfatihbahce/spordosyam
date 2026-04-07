@extends('layouts.panel')

@section('title', 'Ödeme Detayı')
@section('page-title', 'Ödeme Detayı')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.payments.index') }}" class="text-indigo-600 hover:text-indigo-900">← Geri Dön</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Ödeme Bilgileri</h3>
        <dl class="space-y-3">
            <div>
                <dt class="text-sm font-medium text-gray-500">Öğrenci</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $payment->studentFee->student->first_name ?? '-' }} {{ $payment->studentFee->student->last_name ?? '' }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Ödeyen</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $payment->parent->user->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Tutar</dt>
                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ number_format($payment->amount, 2) }} ₺</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Ödeme Yöntemi</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @if($payment->payment_method === 'credit_card')
                        Kredi Kartı
                    @elseif($payment->payment_method === 'bank_transfer')
                        Havale/EFT
                    @else
                        Nakit
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Durum</dt>
                <dd class="mt-1">
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
                </dd>
            </div>
            @if($payment->transaction_id)
            <div>
                <dt class="text-sm font-medium text-gray-500">İşlem ID</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $payment->transaction_id }}</dd>
            </div>
            @endif
            @if($payment->iyzico_payment_id)
            <div>
                <dt class="text-sm font-medium text-gray-500">Iyzico Ödeme ID</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $payment->iyzico_payment_id }}</dd>
            </div>
            @endif
            @if($payment->paid_at)
            <div>
                <dt class="text-sm font-medium text-gray-500">Ödeme Tarihi</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $payment->paid_at->format('d.m.Y H:i') }}</dd>
            </div>
            @endif
            @if($payment->notes)
            <div>
                <dt class="text-sm font-medium text-gray-500">Notlar</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $payment->notes }}</dd>
            </div>
            @endif
        </dl>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Aidat Bilgileri</h3>
        <dl class="space-y-3">
            <div>
                <dt class="text-sm font-medium text-gray-500">Aidat Planı</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $payment->studentFee->fee_label }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Vade Tarihi</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $payment->studentFee->due_date->format('d.m.Y') ?? '-' }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection
