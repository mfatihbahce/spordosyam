@extends('layouts.panel')

@section('title', 'Profil')
@section('page-title', 'Profil Ayarları')

@section('sidebar-menu')
    @include('parent.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('parent.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ad Soyad</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">E-posta</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                    <input type="text" name="phone" value="{{ old('phone', $parent->phone ?? '') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">TC Kimlik No</label>
                    <input type="text" value="{{ $parent->identity_number ? substr($parent->identity_number, 0, 3) . '*****' . substr($parent->identity_number, -2) : '-' }}" 
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-600" readonly disabled>
                    <p class="mt-1 text-xs text-gray-500">Güvenlik nedeniyle TC Kimlik No değiştirilemez. Değişiklik için kurumunuzla iletişime geçin.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adres</label>
                    <textarea name="address" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('address', $parent->address ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mevcut Şifre</label>
                    <input type="password" name="current_password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Şifre değiştirmek istemiyorsanız boş bırakın</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Yeni Şifre</label>
                    <input type="password" name="password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Yeni Şifre (Tekrar)</label>
                    <input type="password" name="password_confirmation" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Güncelle
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
