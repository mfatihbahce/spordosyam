@extends('layouts.panel')

@section('title', 'Çocuğum')
@section('page-title', 'Çocuğum')

@section('sidebar-menu')
    @include('parent.partials.sidebar')
@endsection

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">Çocuğum</h2>
    <p class="text-sm text-gray-500 mt-1">Hoş geldiniz. Kayıtlı öğrencilerinizin bilgileri aşağıdadır.</p>
</div>

@forelse($students as $student)
@php
    $currentEnrollments = $student->currentEnrollments ?? collect();
    $hasActiveClass = $currentEnrollments->count() > 0;
    $firstClass = $currentEnrollments->first()?->classModel ?? $student->classModel;
@endphp
<div class="mb-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        {{-- Kart başlığı --}}
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-5">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold text-white">{{ $student->first_name }} {{ $student->last_name }}</h3>
                        <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $hasActiveClass ? 'bg-green-400/90 text-white' : 'bg-white/30 text-white' }}">
                            {{ $hasActiveClass ? 'Aktif derste kayıtlı' : 'Aktif derse kayıtlı değil' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('parent.attendances.index') }}" class="inline-flex items-center px-3 py-1.5 bg-white/20 text-white text-sm font-medium rounded-lg hover:bg-white/30 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        Yoklamalar
                    </a>
                    <a href="{{ route('parent.payments.index') }}" class="inline-flex items-center px-3 py-1.5 bg-white/20 text-white text-sm font-medium rounded-lg hover:bg-white/30 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Aidatlar
                    </a>
                </div>
            </div>
        </div>

        {{-- Detaylar --}}
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012 2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Sınıf</dt>
                        <dd class="mt-0.5 text-sm font-medium text-gray-900">{{ $firstClass?->name ?? '-' }}</dd>
                    </div>
                </div>
                <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Okul</dt>
                        <dd class="mt-0.5 text-sm text-gray-900">{{ $student->school->name ?? '-' }}</dd>
                    </div>
                </div>
                <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Doğum Tarihi</dt>
                        <dd class="mt-0.5 text-sm text-gray-900">{{ $student->birth_date ? $student->birth_date->format('d.m.Y') : '-' }}</dd>
                    </div>
                </div>
                <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Cinsiyet</dt>
                        <dd class="mt-0.5 text-sm text-gray-900">{{ $student->gender === 'male' ? 'Erkek' : ($student->gender === 'female' ? 'Kız' : '-') }}</dd>
                    </div>
                </div>
                <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0 w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Telefon</dt>
                        <dd class="mt-0.5 text-sm text-gray-900">{{ $student->phone ?? '-' }}</dd>
                    </div>
                </div>
                <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0 w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">E-posta</dt>
                        <dd class="mt-0.5 text-sm text-gray-900 break-all">{{ $student->email ?? '-' }}</dd>
                    </div>
                </div>
                @if($firstClass?->sportBranch)
                <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors sm:col-span-2 lg:col-span-1">
                    <div class="flex-shrink-0 w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Branş</dt>
                        <dd class="mt-0.5 text-sm text-gray-900">{{ $firstClass->sportBranch->name ?? '-' }}</dd>
                    </div>
                </div>
                @endif
                @if(!empty($student->address))
                <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors sm:col-span-2">
                    <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Adres</dt>
                        <dd class="mt-0.5 text-sm text-gray-900">{{ $student->address }}</dd>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@empty
<div class="bg-white rounded-xl shadow-md border border-gray-200 p-12 text-center">
    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
    </div>
    <p class="text-gray-600 font-medium">Henüz kayıtlı öğrenci bulunmamaktadır</p>
    <p class="text-sm text-gray-500 mt-1">Öğrenci kaydı için okulunuzla iletişime geçin.</p>
</div>
@endforelse
@endsection
