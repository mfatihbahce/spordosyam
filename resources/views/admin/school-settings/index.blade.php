@extends('layouts.panel')

@section('title', 'Okul Bilgileri')
@section('page-title', 'Okul Bilgileri')

@section('sidebar-menu')
    @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">Okul Bilgileri</h2>
    <p class="text-sm text-gray-500 mt-1">Okul bilgilerinizi ve telafi dersi ayarlarınızı yönetin.</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.school-settings.update') }}" method="POST">
    @csrf
    @method('PUT')

    {{-- Okul Bilgileri --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Temel Bilgiler</h3>
            <p class="text-sm text-gray-500 mt-0.5">Okul adı, iletişim ve adres bilgileri</p>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Okul Adı</label>
                <input type="text" name="name" value="{{ old('name', $school->name) }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                       required>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">E-posta</label>
                    <input type="email" name="email" value="{{ old('email', $school->email) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                    <input type="text" name="phone" value="{{ old('phone', $school->phone) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Adres</label>
                <textarea name="address" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">{{ old('address', $school->address) }}</textarea>
            </div>
        </div>
    </div>

    {{-- Telafi Dersi Ayarları (Switch) --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Telafi Dersi Ayarları</h3>
            <p class="text-sm text-gray-500 mt-0.5">Ders iptal/erteleme ve izinli öğrenciler için telafi dersi sistemi</p>
        </div>
        <div class="p-6">
            <input type="hidden" name="makeup_class_enabled" value="0">
            <div class="flex items-center justify-between cursor-pointer group">
                <div class="flex-1" onclick="document.getElementById('makeup_class_enabled').click();" role="button" tabindex="0">
                    <span class="block text-sm font-medium text-gray-900">Telafi dersi veriliyor</span>
                    <p class="mt-1 text-sm text-gray-500">
                        Bu seçenek açıkken, ders iptal/erteleme ve izinli öğrenciler için telafi dersi sistemi aktif olur.
                    </p>
                </div>
                <div class="ml-6 flex-shrink-0" onclick="document.getElementById('makeup_class_enabled').click();" role="button" tabindex="0">
                    <span class="relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ old('makeup_class_enabled', $school->makeup_class_enabled) ? 'bg-indigo-600' : 'bg-gray-200' }}" role="switch" aria-checked="{{ old('makeup_class_enabled', $school->makeup_class_enabled) ? 'true' : 'false' }}" tabindex="0" id="makeup_switch_track">
                        <span class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ old('makeup_class_enabled', $school->makeup_class_enabled) ? 'translate-x-5' : 'translate-x-1' }}" style="top: 2px;" id="makeup_switch_thumb"></span>
                    </span>
                    <input type="checkbox" name="makeup_class_enabled" id="makeup_class_enabled" value="1" class="sr-only" {{ old('makeup_class_enabled', $school->makeup_class_enabled) ? 'checked' : '' }} onchange="toggleMakeupSwitch(this.checked)">
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
            Güncelle
        </button>
        <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            İptal
        </a>
    </div>
</form>

<script>
function toggleMakeupSwitch(checked) {
    var track = document.getElementById('makeup_switch_track');
    var thumb = document.getElementById('makeup_switch_thumb');
    if (track && thumb) {
        if (checked) {
            track.classList.remove('bg-gray-200');
            track.classList.add('bg-indigo-600');
            track.setAttribute('aria-checked', 'true');
            thumb.classList.remove('translate-x-1');
            thumb.classList.add('translate-x-5');
        } else {
            track.classList.remove('bg-indigo-600');
            track.classList.add('bg-gray-200');
            track.setAttribute('aria-checked', 'false');
            thumb.classList.remove('translate-x-5');
            thumb.classList.add('translate-x-1');
        }
    }
}
</script>
@endsection
