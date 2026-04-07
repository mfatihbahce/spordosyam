@extends('layouts.panel')

@section('title', 'Medya Detayı')
@section('page-title', $media->title)
@section('page-description', 'Medya detaylarını görüntüleyin')

@section('sidebar-menu')
@include('parent.partials.sidebar')
@endsection

@section('content')
<!-- Breadcrumb -->
<nav class="mb-6 flex items-center space-x-2 text-sm">
    <a href="{{ route('parent.media.index') }}" class="text-indigo-600 hover:text-indigo-800 transition-colors">
        <i class="fas fa-arrow-left mr-1"></i> Medya Listesi
    </a>
    <span class="text-gray-400">/</span>
    <span class="text-gray-600">{{ Str::limit($media->title, 30) }}</span>
</nav>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column - Media Preview (2/3 width on large screens) -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Media Preview Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-image text-indigo-600 mr-2"></i>
                    Medya Önizleme
                </h3>
                
                @if($media->type === 'image')
                    <div class="bg-white rounded-lg p-4 shadow-inner">
                        <img src="{{ media_url($media->file_path) }}" 
                             alt="{{ $media->title }}" 
                             class="w-full h-auto max-h-[600px] object-contain rounded-lg mx-auto shadow-lg"
                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'400\' height=\'200\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'18\' dy=\'10.5\' font-weight=\'bold\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\'%3EGörsel Yüklenemedi%3C/text%3E%3C/svg%3E';">
                    </div>
                    <div class="mt-4 flex justify-center">
                        <a href="{{ media_url($media->file_path) }}" 
                           target="_blank" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            Tam Boyutta Aç
                        </a>
                    </div>
                @elseif($media->type === 'pdf')
                    <div class="bg-white rounded-lg p-8 shadow-inner text-center">
                        <div class="inline-flex items-center justify-center w-24 h-24 bg-red-100 rounded-full mb-4">
                            <i class="fas fa-file-pdf text-red-600 text-4xl"></i>
                        </div>
                        <p class="text-gray-700 font-medium mb-2">{{ $media->file_name }}</p>
                        <p class="text-sm text-gray-500 mb-6">{{ number_format($media->file_size / 1024, 2) }} KB</p>
                        <a href="{{ media_url($media->file_path) }}" 
                           target="_blank" 
                           class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors shadow-md">
                            <i class="fas fa-file-pdf mr-2"></i>
                            PDF'i Aç
                        </a>
                    </div>
                @else
                    <div class="bg-white rounded-lg p-4 shadow-inner">
                        <video controls class="w-full rounded-lg shadow-lg">
                            <source src="{{ media_url($media->file_path) }}" type="{{ $media->mime_type }}">
                            Tarayıcınız video oynatmayı desteklemiyor.
                        </video>
                    </div>
                    <div class="mt-4 flex justify-center">
                        <a href="{{ media_url($media->file_path) }}" 
                           download
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-download mr-2"></i>
                            Videoyu İndir
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column - Media Info (1/3 width on large screens) -->
    <div class="space-y-6">
        <!-- Media Information Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                Medya Bilgileri
            </h3>
            <dl class="space-y-4">
                <div class="pb-4 border-b border-gray-100">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Başlık</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $media->title }}</dd>
                </div>
                
                @if($media->description)
                <div class="pb-4 border-b border-gray-100">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Açıklama</dt>
                    <dd class="text-sm text-gray-700 leading-relaxed">{{ $media->description }}</dd>
                </div>
                @endif
                
                <div class="pb-4 border-b border-gray-100">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Yükleyen</dt>
                    <dd class="text-sm text-gray-700 flex items-center">
                        <i class="fas fa-user text-gray-400 mr-2"></i>
                        {{ $media->uploadedBy->name }}
                    </dd>
                </div>
                
                <div>
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Yüklenme Tarihi</dt>
                    <dd class="text-sm text-gray-700 flex items-center">
                        <i class="fas fa-calendar text-gray-400 mr-2"></i>
                        {{ $media->created_at->format('d.m.Y H:i') }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
