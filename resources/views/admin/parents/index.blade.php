@extends('layouts.panel')

@section('title', 'Veliler')
@section('page-title', 'Veliler')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <h3 class="text-lg font-semibold">Tüm Veliler</h3>
    <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
        <form action="{{ route('admin.parents.index') }}" method="GET" class="flex gap-2 flex-1 sm:flex-initial max-w-md">
            <input type="text" name="search" value="{{ old('search', $search ?? '') }}" placeholder="Veli adı, telefon, öğrenci adı veya TC ile ara..." class="flex-1 min-w-0 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <button type="submit" class="px-4 py-2 bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 whitespace-nowrap">Ara</button>
        </form>
        <a href="{{ route('admin.parents.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-center whitespace-nowrap">
            + Yeni Veli Ekle
        </a>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ad Soyad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefon</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Öğrenci Sayısı</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($parents as $parent)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $parent->user->name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $parent->user->email }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $parent->phone ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $parent->students->count() }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @php
                        $hasActiveStudent = $parent->students->contains(fn($s) => ($s->currentEnrollments ?? collect())->count() > 0);
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $hasActiveStudent ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                        {{ $hasActiveStudent ? 'Aktif öğrencisi var' : 'Aktif öğrencisi bulunmamakta' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('admin.parents.show', $parent) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Görüntüle</a>
                    <a href="{{ route('admin.parents.edit', $parent) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Düzenle</a>
                    <form action="{{ route('admin.parents.destroy', $parent) }}" method="POST" class="inline" onsubmit="return confirm('Bu veliyi silmek istediğinize emin misiniz?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Henüz veli bulunmamaktadır.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $parents->links() }}
    </div>
</div>
@endsection
