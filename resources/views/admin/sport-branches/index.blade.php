@extends('layouts.panel')

@section('title', 'Branşlar')
@section('page-title', 'Branşlar')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4 flex justify-between items-center">
    <h3 class="text-lg font-semibold">Tüm Branşlar</h3>
    <a href="{{ route('admin.sport-branches.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
        + Yeni Branş Ekle
    </a>
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Branş Adı</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Açıklama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sınıf Sayısı</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($sportBranches as $sportBranch)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $sportBranch->name }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ Str::limit($sportBranch->description, 50) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sportBranch->classes_count }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sportBranch->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $sportBranch->is_active ? 'Aktif' : 'Pasif' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('admin.sport-branches.show', $sportBranch) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Görüntüle</a>
                    <a href="{{ route('admin.sport-branches.edit', $sportBranch) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Düzenle</a>
                    <form action="{{ route('admin.sport-branches.destroy', $sportBranch) }}" method="POST" class="inline" onsubmit="return confirm('Bu branşı silmek istediğinize emin misiniz?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Henüz branş bulunmamaktadır.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $sportBranches->links() }}
    </div>
</div>
@endsection
