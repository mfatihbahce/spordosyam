@extends('layouts.panel')

@section('title', 'Öğrenci Aidatı Detayı')
@section('page-title', 'Aidat Detayı')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.student-fees.index') }}" class="text-indigo-600 hover:text-indigo-900 inline-flex items-center">
        <i class="fas fa-arrow-left mr-1.5 text-sm"></i> Geri Dön
    </a>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.students.show', $studentFee->student) }}" class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-200">
            <i class="fas fa-user-graduate mr-1"></i> Öğrenci Detayı
        </a>
        <a href="{{ route('admin.student-fees.edit', $studentFee) }}" class="px-3 py-1.5 bg-amber-100 text-amber-800 rounded-lg text-sm font-medium hover:bg-amber-200">
            <i class="fas fa-edit mr-1"></i> Düzenle
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Aidat Bilgileri --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-5 py-3 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900 flex items-center">
                <i class="fas fa-file-invoice-dollar text-indigo-600 mr-2 text-sm"></i>
                Aidat Bilgileri
            </h3>
        </div>
        <div class="p-5">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Öğrenci</dt>
                    <dd class="mt-1">
                        <a href="{{ route('admin.students.show', $studentFee->student) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            {{ $studentFee->student->first_name }} {{ $studentFee->student->last_name }}
                        </a>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Aidat</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $studentFee->fee_label }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tutar</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900">₺{{ number_format($studentFee->amount, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Vade Tarihi</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $studentFee->due_date->format('d.m.Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Durum</dt>
                    <dd class="mt-1">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'paid' => 'bg-green-100 text-green-800',
                                'overdue' => 'bg-red-100 text-red-800',
                                'cancelled' => 'bg-gray-100 text-gray-700',
                            ];
                            $statusLabels = [
                                'pending' => 'Beklemede',
                                'paid' => 'Ödendi',
                                'overdue' => 'Gecikmiş',
                                'cancelled' => 'İptal',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$studentFee->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$studentFee->status] ?? $studentFee->status }}
                        </span>
                    </dd>
                </div>
                @if($studentFee->notes)
                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Not</dt>
                    <dd class="mt-1 text-sm text-gray-700">{{ $studentFee->notes }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    {{-- Ödeme Bilgileri --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-5 py-3 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900 flex items-center">
                <i class="fas fa-credit-card text-slate-600 mr-2 text-sm"></i>
                Ödeme Bilgileri
            </h3>
        </div>
        <div class="p-5">
            @if($studentFee->payments->count() > 0)
            <ul class="space-y-3">
                @foreach($studentFee->payments as $payment)
                <li class="flex items-center justify-between p-4 rounded-lg border border-gray-100 bg-gray-50/50">
                    <div>
                        <span class="text-sm font-semibold text-gray-900">₺{{ number_format($payment->amount, 2) }}</span>
                        @if($payment->parent && $payment->parent->user)
                            <p class="text-xs text-gray-500 mt-0.5">Ödeyen: {{ $payment->parent->user->name }}</p>
                        @endif
                        @if($payment->paid_at)
                            <p class="text-xs text-gray-500">Tarih: {{ $payment->paid_at->format('d.m.Y H:i') }}</p>
                        @endif
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : ($payment->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ $payment->status === 'completed' ? 'Tamamlandı' : ($payment->status === 'failed' ? 'Başarısız' : 'Beklemede') }}
                    </span>
                </li>
                @endforeach
            </ul>
            @else
            <div class="text-center py-8">
                <i class="fas fa-wallet text-gray-300 text-3xl mb-3 block"></i>
                <p class="text-sm text-gray-500">Henüz ödeme yapılmamış.</p>
                <p class="text-xs text-gray-400 mt-1">Veli panelden bu aidatı ödeyebilir.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
