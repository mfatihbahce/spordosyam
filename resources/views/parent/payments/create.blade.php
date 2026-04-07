@extends('layouts.panel')

@section('title', 'Ödeme Yap')
@section('page-title', 'Ödeme Yap')

@section('sidebar-menu')
@include('parent.partials.sidebar')
@endsection

@section('content')
<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('parent.payments.index') }}" class="text-indigo-600 hover:text-indigo-900 inline-flex items-center text-sm font-medium">
        <i class="fas fa-arrow-left mr-2"></i> Geri Dön
    </a>
</div>

@if(session('error'))
    <div class="mb-6 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
    {{-- Aidat Bilgileri --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-5 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900 flex items-center">
                <i class="fas fa-file-invoice-dollar text-indigo-600 mr-2"></i>
                Aidat Bilgileri
            </h3>
            <p class="text-xs text-gray-500 mt-1">Ödeyeceğiniz aidat özeti</p>
        </div>
        <div class="p-5">
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Öğrenci</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $studentFee->student->first_name }} {{ $studentFee->student->last_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Aidat</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $studentFee->fee_label }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Vade Tarihi</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $studentFee->due_date->format('d.m.Y') }}</dd>
                </div>
                <div class="pt-4 border-t border-gray-100">
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ödenecek Tutar</dt>
                    <dd class="mt-2 inline-block px-4 py-3 rounded-xl bg-indigo-50 border border-indigo-100">
                        <span class="text-2xl font-bold text-indigo-700">₺{{ number_format($studentFee->amount, 2) }}</span>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Kart Bilgileri --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-5 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900 flex items-center">
                <i class="fas fa-credit-card text-slate-600 mr-2"></i>
                Kart Bilgileri
            </h3>
            <p class="text-xs text-gray-500 mt-1">Kredi veya banka kartı ile güvenli ödeme</p>
        </div>
        <div class="p-5">
            <form action="{{ route('parent.payments.store', $studentFee) }}" method="POST" id="paymentForm">
                @csrf

                <div class="space-y-5">
                    <div>
                        <label for="card_holder_name" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Kart Sahibi Adı Soyadı <span class="text-red-500">*</span></label>
                        <input type="text" name="card_holder_name" id="card_holder_name" required
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm px-3 py-2.5 border"
                               value="{{ old('card_holder_name') }}" placeholder="Ad Soyad">
                        @error('card_holder_name')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="card_number" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Kart Numarası <span class="text-red-500">*</span></label>
                        <input type="text" name="card_number" id="card_number" required maxlength="19"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm px-3 py-2.5 border tracking-widest font-mono"
                               value="{{ old('card_number') }}" placeholder="0000 0000 0000 0000"
                               oninput="formatCardNumber(this)">
                        @error('card_number')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="expire_month" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Ay <span class="text-red-500">*</span></label>
                            <input type="text" name="expire_month" id="expire_month" required maxlength="2"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm px-3 py-2.5 border text-center"
                                   value="{{ old('expire_month') }}" placeholder="MM">
                            @error('expire_month')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="expire_year" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">Yıl <span class="text-red-500">*</span></label>
                            <input type="text" name="expire_year" id="expire_year" required maxlength="4"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm px-3 py-2.5 border text-center"
                                   value="{{ old('expire_year') }}" placeholder="YYYY">
                            @error('expire_year')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="cvc" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5">CVC <span class="text-red-500">*</span></label>
                            <input type="text" name="cvc" id="cvc" required maxlength="4"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm px-3 py-2.5 border text-center"
                                   value="{{ old('cvc') }}" placeholder="•••"
                                   autocomplete="off">
                            @error('cvc')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-5 border-t border-gray-100">
                    <button type="submit" class="w-full flex items-center justify-center py-3.5 px-4 rounded-xl bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-lock mr-2 text-indigo-200"></i>
                        <span id="submitBtnText">₺{{ number_format($studentFee->amount, 2) }} Öde</span>
                    </button>
                </div>

                <p class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-shield-alt text-gray-400"></i>
                    Ödeme işlemi Iyzico güvenli ödeme altyapısı ile gerçekleştirilmektedir.
                </p>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function formatCardNumber(input) {
    let value = input.value.replace(/\D/g, '').slice(0, 16);
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    input.value = formattedValue;
}

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    var btn = this.querySelector('button[type="submit"]');
    var text = document.getElementById('submitBtnText');
    if (btn && text) {
        btn.disabled = true;
        text.textContent = 'İşleniyor...';
    }
});
</script>
@endpush
@endsection
