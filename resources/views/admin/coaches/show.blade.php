@extends('layouts.panel')

@section('title', 'Antrenör Detayı')
@section('page-title', $coach->user->name)

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.coaches.index') }}" class="text-indigo-600 hover:text-indigo-900">← Geri Dön</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Antrenör Bilgileri</h3>
        <dl class="space-y-3">
            <div>
                <dt class="text-sm font-medium text-gray-500">Ad Soyad</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $coach->user->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Email</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $coach->user->email }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Telefon</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $coach->phone ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Biyografi</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $coach->bio ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Durum</dt>
                <dd class="mt-1">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $coach->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $coach->is_active ? 'Aktif' : 'Pasif' }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">İstatistikler</h3>
        <dl class="space-y-3">
            <div>
                <dt class="text-sm font-medium text-gray-500">Atanan Sınıf</dt>
                <dd class="mt-1 text-2xl font-bold text-gray-900">{{ $coach->classes->count() }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Toplam Yoklama</dt>
                <dd class="mt-1 text-2xl font-bold text-gray-900">{{ $coach->attendances->count() }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection
