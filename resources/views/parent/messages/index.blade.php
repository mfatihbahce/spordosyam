@extends('layouts.panel')

@section('title', 'Mesajlar')
@section('page-title', 'Mesajlar')

@section('sidebar-menu')
    @include('parent.partials.sidebar')
@endsection

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Mesajlar</h2>
        <p class="text-sm text-gray-500 mt-1">Öğrencinizin antrenörleriyle mesajlaşın.</p>
    </div>
    <a href="{{ route('parent.messages.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8x"></path></svg>
        Yeni Mesaj
    </a>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
@endif

@if($conversations->isEmpty())
<div class="bg-white rounded-xl shadow-md border border-gray-200 p-12 text-center">
    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
    </div>
    <p class="text-gray-600 font-medium">Henüz mesajınız yok</p>
    <p class="text-sm text-gray-500 mt-1">Antrenöre mesaj göndermek için "Yeni Mesaj" butonunu kullanın.</p>
    <a href="{{ route('parent.messages.create') }}" class="inline-flex items-center mt-4 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Yeni Mesaj</a>
</div>
@else
<div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
    <ul class="divide-y divide-gray-200">
        @foreach($conversations as $conv)
        <li>
            <a href="{{ route('parent.messages.show', $conv) }}" class="block p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center min-w-0">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-semibold text-sm flex-shrink-0">
                            {{ $conv->coach->user ? strtoupper(mb_substr($conv->coach->user->name, 0, 2)) : 'AN' }}
                        </div>
                        <div class="ml-4 min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $conv->coach->user->name ?? 'Antrenör' }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $conv->student->first_name }} {{ $conv->student->last_name }} · {{ $conv->messages->first()?->body ? Str::limit($conv->messages->first()->body, 50) : '—' }}</p>
                        </div>
                    </div>
                    <div class="flex-shrink-0 ml-4 text-right">
                        @if($conv->last_message_at)
                        <span class="text-xs text-gray-500">{{ $conv->last_message_at->locale('tr')->diffForHumans() }}</span>
                        @endif
                        <svg class="w-5 h-5 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </div>
            </a>
        </li>
        @endforeach
    </ul>
</div>
@endif
@endsection
