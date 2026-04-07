@extends('layouts.panel')

@section('title', 'Okul Düzenle')
@section('page-title', $school->name)
@section('page-description', 'Okul bilgilerini düzenleyin')

@section('sidebar-menu')
@include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <a href="{{ route('superadmin.schools.show', $school) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>Okul detayına dön
    </a>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-2xl text-sm flex items-center shadow-sm">
        <i class="fas fa-check-circle mr-3 text-green-600 text-lg"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm shadow-sm">
        <p class="font-medium mb-2"><i class="fas fa-exclamation-circle mr-2"></i>Lütfen formdaki hataları düzeltin.</p>
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden w-full">
    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
        <h3 class="text-lg font-semibold text-slate-800">Okul bilgileri</h3>
        <p class="text-sm text-slate-500 mt-0.5">Ad, iletişim ve açıklama alanlarını güncelleyebilirsiniz. Lisans uzatımı okul detay sayfasından yapılır.</p>
    </div>

    <form action="{{ route('superadmin.schools.update', $school) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')

        <div class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Okul adı <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" required
                           class="block w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           value="{{ old('name', $school->name) }}" placeholder="Örn. Spor Okulu">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">E-posta <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" required
                           class="block w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           value="{{ old('email', $school->email) }}" placeholder="okul@ornek.com">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="phone" class="block text-sm font-semibold text-slate-700 mb-2">Telefon</label>
                <input type="text" name="phone" id="phone" maxlength="20"
                       class="block w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                       value="{{ old('phone', $school->phone) }}" placeholder="0532 000 00 00">
                @error('phone')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="address" class="block text-sm font-semibold text-slate-700 mb-2">Adres</label>
                <textarea name="address" id="address" rows="3"
                          class="block w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                          placeholder="Okul adresi">{{ old('address', $school->address) }}</textarea>
                @error('address')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Açıklama</label>
                <textarea name="description" id="description" rows="4"
                          class="block w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                          placeholder="Okul hakkında kısa açıklama">{{ old('description', $school->description) }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50 border border-slate-100">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                       {{ old('is_active', $school->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="text-sm font-medium text-slate-700">Okul aktif (sistemde görünsün ve giriş yapılabilsin)</label>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-200 flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 shadow-md transition-colors">
                <i class="fas fa-save mr-2"></i>Güncelle
            </button>
            <a href="{{ route('superadmin.schools.show', $school) }}" class="inline-flex items-center px-5 py-2.5 bg-white border-2 border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">
                İptal
            </a>
        </div>
    </form>
</div>

@endsection
