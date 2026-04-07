@extends('layouts.panel')

@section('title', 'Medya Detayı')
@section('page-title', $media->title)
@section('page-description', 'Medya detaylarını görüntüleyin')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<!-- Breadcrumb -->
<nav class="mb-6 flex items-center space-x-2 text-sm">
    <a href="{{ route('admin.media.index') }}" class="text-indigo-600 hover:text-indigo-800 transition-colors">
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
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tip</dt>
                    <dd class="text-sm">
                        @if($media->type === 'image')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-image mr-1"></i> Görsel
                            </span>
                        @elseif($media->type === 'pdf')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-file-pdf mr-1"></i> PDF
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-video mr-1"></i> Video
                            </span>
                        @endif
                    </dd>
                </div>
                
                <div class="pb-4 border-b border-gray-100">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Dosya Adı</dt>
                    <dd class="text-sm text-gray-700 break-all">{{ $media->file_name }}</dd>
                </div>
                
                <div class="pb-4 border-b border-gray-100">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Dosya Boyutu</dt>
                    <dd class="text-sm text-gray-700">
                        <i class="fas fa-weight text-gray-400 mr-1"></i>
                        {{ number_format($media->file_size / 1024, 2) }} KB
                    </dd>
                </div>
                
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

        <!-- Targets Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-bullseye text-indigo-600 mr-2"></i>
                Paylaşım Hedefleri
            </h3>
            @if($media->targets->count() > 0)
                <div class="space-y-2">
                    @foreach($media->targets as $target)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center">
                            @if($target->target_type === 'branch')
                                <i class="fas fa-building text-indigo-600 mr-2"></i>
                                <span class="text-sm font-medium text-gray-700">Şube</span>
                            @elseif($target->target_type === 'sport_branch')
                                <i class="fas fa-futbol text-indigo-600 mr-2"></i>
                                <span class="text-sm font-medium text-gray-700">Branş</span>
                            @elseif($target->target_type === 'class')
                                <i class="fas fa-users text-indigo-600 mr-2"></i>
                                <span class="text-sm font-medium text-gray-700">Sınıf</span>
                            @else
                                <i class="fas fa-user-graduate text-indigo-600 mr-2"></i>
                                <span class="text-sm font-medium text-gray-700">Öğrenci</span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">ID: {{ $target->target_id }}</span>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <i class="fas fa-globe text-gray-300 text-3xl mb-2"></i>
                    <p class="text-sm text-gray-500">Herkes görebilir</p>
                    <p class="text-xs text-gray-400 mt-1">Özel bir hedef belirlenmemiş</p>
                </div>
            @endif
        </div>

        <!-- Actions Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-cog text-indigo-600 mr-2"></i>
                İşlemler
            </h3>
            <form action="{{ route('admin.media.destroy', $media->id) }}" method="POST" onsubmit="return confirm('Bu medyayı silmek istediğinize emin misiniz?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash"></i>
                    <span>Medyayı Sil</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
