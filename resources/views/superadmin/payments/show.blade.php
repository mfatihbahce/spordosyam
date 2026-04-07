@extends('layouts.panel')

@section('title', 'Ödeme Detayı')
@section('page-title', 'Ödeme Detayı')

@section('sidebar-menu')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ödeme Bilgileri</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Ödeme ID</dt>
                <dd class="mt-1 text-sm text-gray-900">#{{ $payment->id }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Durum</dt>
                <dd class="mt-1">
                    @if($payment->status == 'completed')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Tamamlandı</span>
                    @elseif($payment->status == 'pending')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Bekliyor</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Başarısız</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Tutar</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ number_format($payment->amount, 2) }} ₺</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Ödeme Yöntemi</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($payment->payment_method) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Okul</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $payment->school->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Öğrenci</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $payment->studentFee->student->first_name ?? '-' }} {{ $payment->studentFee->student->last_name ?? '' }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Veli</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $payment->parent->user->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">İşlem Tarihi</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $payment->created_at->format('d.m.Y H:i') }}</dd>
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
        </dl>
        
        @if($payment->notes)
        <div class="mt-4">
            <dt class="text-sm font-medium text-gray-500">Notlar</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $payment->notes }}</dd>
        </div>
        @endif
    </div>
    
    <div class="flex justify-end">
        <a href="{{ route('superadmin.payments.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            Geri Dön
        </a>
    </div>
</div>
@endsection
