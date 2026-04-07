@extends('layouts.panel')

@section('title', 'Kullanıcı Detayı')
@section('page-title', 'Kullanıcı Detayı')

@section('sidebar-menu')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Kullanıcı Bilgileri</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Ad Soyad</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">E-posta</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Rol</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($user->role) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Okul</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $user->school->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Durum</dt>
                <dd class="mt-1">
                    @if($user->is_active)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pasif</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Kayıt Tarihi</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d.m.Y H:i') }}</dd>
            </div>
        </dl>
    </div>
    <div class="flex justify-end mt-6">
        <a href="{{ route('superadmin.users.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            Geri Dön
        </a>
    </div>
</div>
@endsection
