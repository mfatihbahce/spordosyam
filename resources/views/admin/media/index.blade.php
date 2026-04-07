@extends('layouts.panel')

@section('title', 'Medya')
@section('page-title', 'Medya')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4 flex justify-between items-center">
    <h3 class="text-lg font-semibold">Tüm Medya</h3>
    <a href="{{ route('admin.media.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
        + Yeni Medya Yükle
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
    @forelse($media as $item)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($item->type === 'image')
            <img src="{{ media_url($item->file_path) }}" alt="{{ $item->title }}" class="w-full h-24 object-cover" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'400\' height=\'200\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'18\' dy=\'10.5\' font-weight=\'bold\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\'%3EGörsel Yüklenemedi%3C/text%3E%3C/svg%3E';">
        @elseif($item->type === 'pdf')
            <div class="w-full h-24 bg-red-100 flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-8 h-8 mx-auto text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-xs text-gray-600 mt-1">PDF</p>
                </div>
            </div>
        @else
            <div class="w-full h-24 bg-blue-100 flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-8 h-8 mx-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-xs text-gray-600 mt-1">Video</p>
                </div>
            </div>
        @endif
        <div class="p-3">
            <h4 class="font-semibold text-gray-900 mb-1 text-sm leading-tight">{{ Str::limit($item->title, 25) }}</h4>
            @if($item->description)
                <p class="text-xs text-gray-600 mb-2 line-clamp-2">{{ Str::limit($item->description, 50) }}</p>
            @endif
            <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                <span class="truncate">{{ Str::limit($item->uploadedBy->name, 10) }}</span>
                <span>{{ $item->created_at->format('d.m.Y') }}</span>
            </div>
            <div class="flex space-x-1">
                <a href="{{ route('admin.media.show', $item->id) }}" class="flex-1 bg-indigo-600 text-white text-center px-2 py-1.5 rounded hover:bg-indigo-700 text-xs">
                    Görüntüle
                </a>
                <form action="{{ route('admin.media.destroy', $item->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Bu medyayı silmek istediğinize emin misiniz?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 text-white px-2 py-1.5 rounded hover:bg-red-700 text-xs">
                        Sil
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-12">
        <p class="text-gray-500">Henüz medya yüklenmemiş.</p>
    </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $media->links() }}
</div>
@endsection
