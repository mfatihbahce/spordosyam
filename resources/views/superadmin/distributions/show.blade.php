@extends('layouts.panel')

@section('title', 'Dağıtım Detayı')
@section('page-title', 'Dağıtım Detayı')

@section('sidebar-menu')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Dağıtım Bilgileri</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Dağıtım ID</dt>
                <dd class="mt-1 text-sm text-gray-900">#{{ $distribution->id }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Durum</dt>
                <dd class="mt-1">
                    @if($distribution->status == 'completed')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Tamamlandı</span>
                    @elseif($distribution->status == 'pending')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Bekliyor</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ $distribution->status }}</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Okul</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $distribution->school->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Banka</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $distribution->bankAccount->bank_name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">IBAN</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $distribution->bankAccount->iban ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Tutar</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ number_format($distribution->amount, 2) }} ₺</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Komisyon</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ number_format($distribution->commission, 2) }} ₺</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Net Tutar</dt>
                <dd class="mt-1 text-sm font-semibold text-indigo-600">{{ number_format($distribution->net_amount, 2) }} ₺</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Oluşturulma Tarihi</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $distribution->created_at->format('d.m.Y H:i') }}</dd>
            </div>
            @if($distribution->processed_at)
            <div>
                <dt class="text-sm font-medium text-gray-500">İşlem Tarihi</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $distribution->processed_at->format('d.m.Y H:i') }}</dd>
            </div>
            @endif
            @if($distribution->iyzico_transfer_id)
            <div>
                <dt class="text-sm font-medium text-gray-500">Iyzico Transfer ID</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $distribution->iyzico_transfer_id }}</dd>
            </div>
            @endif
        </dl>
        
        @if($distribution->notes)
        <div class="mt-4">
            <dt class="text-sm font-medium text-gray-500">Notlar</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $distribution->notes }}</dd>
        </div>
        @endif
    </div>
    
    <div class="flex justify-end">
        <a href="{{ route('superadmin.distributions.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            Geri Dön
        </a>
    </div>
</div>
@endsection
