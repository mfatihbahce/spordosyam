@extends('layouts.panel')

@section('title', 'Başvuru Detayı')
@section('page-title', 'Başvuru Detayı')
@section('page-description', 'Başvuru bilgileri ve lisans tanımlama')

@section('sidebar-menu')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('superadmin.applications.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>Geri Dön
    </a>
    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
        {{ $application->status === 'approved' ? 'bg-green-100 text-green-800' : ($application->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
        {{ $application->status === 'approved' ? 'Onaylandı' : ($application->status === 'rejected' ? 'Reddedildi' : 'Beklemede') }}
    </span>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-center">
        <i class="fas fa-check-circle mr-2 text-green-600"></i>{{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-center">
        <i class="fas fa-exclamation-circle mr-2 text-red-600"></i>{{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Başvuru Bilgileri --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-semibold text-gray-800">Başvuru Bilgileri</h3>
            <p class="text-sm text-gray-500 mt-0.5">Talep eden okul ve iletişim bilgileri</p>
        </div>
        <div class="p-6">
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Okul Adı</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $application->school_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">İletişim Kişisi</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $application->contact_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">E-posta</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $application->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Telefon</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $application->phone }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Adres</dt>
                    <dd class="mt-1 text-sm text-gray-700">{{ $application->address ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Başvuru Tarihi</dt>
                    <dd class="mt-1 text-sm text-gray-700">{{ $application->created_at->format('d.m.Y H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Mesaj --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-semibold text-gray-800">Mesaj</h3>
            <p class="text-sm text-gray-500 mt-0.5">Başvuru sahibinin notları</p>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-700 leading-relaxed">{{ $application->message ?: 'Mesaj bulunmamaktadır.' }}</p>
        </div>
    </div>
</div>

@if($application->status === 'approved')
    @php
        $expiresAt = $school ? $school->demo_expires_at : $application->demo_expires_at;
        $daysLeft = $expiresAt ? (int) now()->startOfDay()->diffInDays($expiresAt->copy()->startOfDay(), false) : null;
        $licenseTypeLabels = ['demo' => 'Demo', 'free' => 'Ücretsiz lisans', 'paid' => 'Ücretli lisans'];
        $licenseTypeIcons = ['demo' => 'fa-vial', 'free' => 'fa-gift', 'paid' => 'fa-lira-sign'];
    @endphp
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50/50 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Lisans Bilgileri</h3>
                <p class="text-sm text-gray-500 mt-0.5">Süre, bitiş tarihi ve güncel durum</p>
            </div>
            @if($school)
                <a href="{{ route('superadmin.schools.show', $school) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-school mr-2"></i>Okula Git
                </a>
            @endif
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Lisans Türü</dt>
                    <dd class="mt-1 flex items-center gap-2">
                        <i class="fas {{ $licenseTypeIcons[$application->license_type ?? 'demo'] ?? 'fa-key' }} text-indigo-500"></i>
                        <span class="text-sm font-medium text-gray-900">{{ $licenseTypeLabels[$application->license_type ?? 'demo'] ?? $application->license_type }}</span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tanımlanan Süre</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $application->demo_days ? $application->demo_days . ' gün' : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Bitiş Tarihi</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $expiresAt ? $expiresAt->format('d.m.Y') : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Onay Tarihi</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $application->approved_at ? $application->approved_at->format('d.m.Y H:i') : '—' }}</dd>
                </div>
                @if(($application->license_type ?? '') === 'paid' && $application->paid_amount !== null)
                <div class="md:col-span-2">
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Anlaşma Tutarı</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ number_format((float) $application->paid_amount, 2, ',', '.') }} ₺</dd>
                </div>
                @endif
            </div>
            <div class="mt-6 pt-6 border-t border-gray-200 flex flex-wrap items-center gap-3">
                <span class="text-sm font-medium text-gray-700">Durum:</span>
                @if($daysLeft === null)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700">
                        <i class="fas fa-minus-circle mr-1.5 text-gray-500"></i>Bitiş tarihi yok
                    </span>
                @elseif($daysLeft < 0)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <i class="fas fa-times-circle mr-1.5 text-red-600"></i>Süresi dolmuş ({{ abs($daysLeft) }} gün önce)
                    </span>
                @elseif($daysLeft <= 10)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                        <i class="fas fa-exclamation-triangle mr-1.5 text-amber-600"></i>{{ $daysLeft }} gün kaldı
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1.5 text-green-600"></i>Aktif ({{ $daysLeft }} gün kaldı)
                    </span>
                @endif
            </div>
        </div>
    </div>
@endif

@if($application->status === 'pending')
<div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50/50">
        <h3 class="text-lg font-semibold text-gray-800">Başvuruyu Onayla ve Lisans Tanımla</h3>
        <p class="text-sm text-gray-500 mt-0.5">Lisans türü seçin; onay sonrası okul ve admin hesabı oluşturulacak.</p>
    </div>
    <div class="p-6">
        <form action="{{ route('superadmin.applications.approve', $application) }}" method="POST" id="approveForm" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Lisans Türü</label>
                @php $currentType = request()->old('license_type', 'demo'); @endphp
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="relative flex cursor-pointer rounded-xl border-2 p-4 transition-colors focus-within:ring-2 focus-within:ring-indigo-500 {{ $currentType === 'demo' ? 'border-indigo-600 bg-indigo-50/50' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" name="license_type" value="demo" class="sr-only" {{ $currentType === 'demo' ? 'checked' : '' }}>
                        <div class="flex-1">
                            <span class="block font-medium text-gray-900">Demo</span>
                            <span class="mt-1 block text-xs text-gray-500">Varsayılan {{ $defaultDemoDays }} gün (Genel Ayarlar'dan)</span>
                        </div>
                        <i class="fas fa-vial text-indigo-500 text-lg absolute top-4 right-4 opacity-60"></i>
                    </label>
                    <label class="relative flex cursor-pointer rounded-xl border-2 p-4 transition-colors focus-within:ring-2 focus-within:ring-indigo-500 {{ $currentType === 'free' ? 'border-indigo-600 bg-indigo-50/50' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" name="license_type" value="free" class="sr-only" {{ $currentType === 'free' ? 'checked' : '' }}>
                        <div class="flex-1">
                            <span class="block font-medium text-gray-900">Ücretsiz lisans</span>
                            <span class="mt-1 block text-xs text-gray-500">Bitime 10 gün kala uyarı</span>
                        </div>
                        <i class="fas fa-gift text-green-500 text-lg absolute top-4 right-4 opacity-60"></i>
                    </label>
                    <label class="relative flex cursor-pointer rounded-xl border-2 p-4 transition-colors focus-within:ring-2 focus-within:ring-indigo-500 {{ $currentType === 'paid' ? 'border-indigo-600 bg-indigo-50/50' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" name="license_type" value="paid" class="sr-only" {{ $currentType === 'paid' ? 'checked' : '' }}>
                        <div class="flex-1">
                            <span class="block font-medium text-gray-900">Ücretli lisans</span>
                            <span class="mt-1 block text-xs text-gray-500">Anlaşma tutarı + süre</span>
                        </div>
                        <i class="fas fa-lira-sign text-amber-500 text-lg absolute top-4 right-4 opacity-60"></i>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div id="license_days_wrap">
                    <label for="license_days" class="block text-sm font-medium text-gray-700 mb-2">Süre (Gün)</label>
                    <input type="number" name="license_days" id="license_days" min="1" max="365" value="{{ request()->old('license_days', $defaultDemoDays) }}"
                           class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <p id="demo_days_hint" class="mt-2 text-xs text-gray-500">Demo seçiliyken süre Genel Ayarlar'daki varsayılan ({{ $defaultDemoDays }} gün) kullanılır.</p>
                    @error('license_days')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div id="paid_amount_wrap" class="hidden">
                    <label for="paid_amount" class="block text-sm font-medium text-gray-700 mb-2">Anlaşma Tutarı (₺)</label>
                    <input type="number" name="paid_amount" id="paid_amount" min="0" step="0.01" value="{{ request()->old('paid_amount') }}"
                           class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           placeholder="Örn. 5000.00">
                    @error('paid_amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-gray-200">
                <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                    <i class="fas fa-check mr-2"></i>Onayla ve Okul Oluştur
                </button>
                <form action="{{ route('superadmin.applications.reject', $application) }}" method="POST" class="inline" onsubmit="return confirm('Bu başvuruyu reddetmek istediğinize emin misiniz?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition-colors">
                        <i class="fas fa-times mr-2"></i>Reddet
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    var form = document.getElementById('approveForm');
    if (!form) return;
    var radios = form.querySelectorAll('input[name="license_type"]');
    var paidWrap = document.getElementById('paid_amount_wrap');
    var paidInput = document.getElementById('paid_amount');
    var daysInput = document.getElementById('license_days');
    var hint = document.getElementById('demo_days_hint');
    var labels = form.querySelectorAll('label.relative.flex.cursor-pointer');

    function updateUi(licenseType) {
        labels.forEach(function(lab) {
            var radio = lab.querySelector('input[type="radio"]');
            if (radio && radio.value === licenseType) {
                lab.classList.add('border-indigo-600', 'bg-indigo-50/50');
                lab.classList.remove('border-gray-200');
            } else {
                lab.classList.remove('border-indigo-600', 'bg-indigo-50/50');
                lab.classList.add('border-gray-200');
            }
        });
        if (licenseType === 'paid') {
            paidWrap.classList.remove('hidden');
            paidInput.setAttribute('required', 'required');
            daysInput.removeAttribute('required');
            if (hint) hint.classList.add('hidden');
        } else if (licenseType === 'demo') {
            paidWrap.classList.add('hidden');
            paidInput.removeAttribute('required');
            daysInput.removeAttribute('required');
            if (hint) hint.classList.remove('hidden');
        } else {
            paidWrap.classList.add('hidden');
            paidInput.removeAttribute('required');
            paidInput.value = '';
            daysInput.setAttribute('required', 'required');
            if (hint) hint.classList.add('hidden');
        }
    }

    radios.forEach(function(radio) {
        radio.addEventListener('change', function() { updateUi(this.value); });
    });
    var selected = form.querySelector('input[name="license_type"]:checked');
    updateUi(selected ? selected.value : 'demo');
})();
</script>
@endif
@endsection
