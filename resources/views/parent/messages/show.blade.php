@extends('layouts.panel')

@section('title', 'Mesaj')
@section('page-title', 'Mesaj')

@section('sidebar-menu')
    @include('parent.partials.sidebar')
@endsection

@section('content')
<div class="mb-6">
    <a href="{{ route('parent.messages.index') }}" class="text-indigo-600 hover:text-indigo-800 inline-flex items-center font-medium">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Mesajlara Dön
    </a>
</div>

@if(session('success'))
<div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
    {{-- Başlık --}}
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-semibold text-sm flex-shrink-0">
                {{ $message->coach->user ? strtoupper(mb_substr($message->coach->user->name, 0, 2)) : 'AN' }}
            </div>
            <div class="ml-4">
                <h2 class="text-lg font-bold text-gray-800">{{ $message->coach->user->name ?? 'Antrenör' }}</h2>
                <p class="text-sm text-gray-500">{{ $message->student->first_name }} {{ $message->student->last_name }} · Öğrencinizin antrenörü</p>
            </div>
        </div>
    </div>

    {{-- Mesaj listesi --}}
    <div class="p-6 space-y-4 max-h-96 overflow-y-auto">
        @foreach($message->messages as $m)
        <div class="flex {{ $m->isFromParent() ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-[85%] rounded-xl px-4 py-3 {{ $m->isFromParent() ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900' }}">
                <p class="text-xs font-medium opacity-90 mb-1">{{ $m->sender_name }}</p>
                <p class="text-sm whitespace-pre-wrap">{{ $m->body }}</p>
                <p class="text-xs mt-1 opacity-75">{{ $m->created_at->format('d.m.Y H:i') }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Cevap formu --}}
    <div class="p-6 border-t border-gray-200 bg-gray-50">
        <form action="{{ route('parent.messages.reply', $message) }}" method="POST">
            @csrf
            <div class="flex gap-3">
                <textarea name="body" rows="2" required maxlength="2000" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Mesajınızı yazın..."></textarea>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 flex-shrink-0 h-fit">Gönder</button>
            </div>
            @error('body')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </form>
    </div>
</div>
@endsection
