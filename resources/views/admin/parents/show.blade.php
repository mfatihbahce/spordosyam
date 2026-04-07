@extends('layouts.panel')

@section('title', 'Veli Detayı')
@section('page-title', $parent->user->name)

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.parents.index') }}" class="text-indigo-600 hover:text-indigo-900 inline-flex items-center font-medium transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Geri Dön
    </a>
    <a href="{{ route('admin.parents.edit', $parent) }}" class="inline-flex items-center px-4 py-2 bg-amber-100 text-amber-800 rounded-lg text-sm font-medium hover:bg-amber-200 transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
        Düzenle
    </a>
</div>

{{-- Durum --}}
<div class="mb-6">
    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $parent->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            @if($parent->is_active)
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            @else
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            @endif
        </svg>
        {{ $parent->is_active ? 'Aktif' : 'Pasif' }}
    </span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
    {{-- Veli Bilgileri --}}
    <div class="lg:col-span-2 min-h-0 flex flex-col">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 h-full flex flex-col">
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    Veli Bilgileri
                </h3>
            </div>
            <div class="p-6 flex-1 min-h-0">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ad Soyad</dt>
                            <dd class="mt-0.5 text-sm font-semibold text-gray-900">{{ $parent->user->name }}</dd>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Telefon</dt>
                            <dd class="mt-0.5 text-sm text-gray-900">{{ $parent->phone ?? '-' }}</dd>
                        </div>
                    </div>
                    {{-- Alt satır: solda E-posta, sağda Adres --}}
                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">E-posta</dt>
                            <dd class="mt-0.5 text-sm text-gray-900 break-all">{{ $parent->user->email }}</dd>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Adres</dt>
                            <dd class="mt-0.5 text-sm text-gray-900">{{ $parent->address ?? '-' }}</dd>
                        </div>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    {{-- Öğrenciler --}}
    <div class="min-h-0 flex flex-col">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 h-full flex flex-col">
            <div class="bg-gradient-to-r from-slate-50 to-gray-100 px-6 py-4 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    Öğrenciler ({{ $parent->students->count() }})
                </h3>
            </div>
            <div class="p-6 flex-1 min-h-0 overflow-auto">
                @if($parent->students->count() > 0)
                    <ul class="space-y-4">
                        @foreach($parent->students as $student)
                        <li>
                            <a href="{{ route('admin.students.show', $student) }}" class="block p-4 rounded-xl border border-gray-200 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all group">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center min-w-0">
                                        <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-semibold text-sm group-hover:bg-indigo-200 transition-colors">
                                            {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                        </div>
                                        <div class="ml-4 min-w-0">
                                            <p class="font-semibold text-gray-900 group-hover:text-indigo-700 truncate">{{ $student->first_name }} {{ $student->last_name }}</p>
                                            <p class="text-sm text-gray-500 mt-0.5">
                                                {{ $student->pivot->relationship === 'mother' ? 'Anne' : ($student->pivot->relationship === 'father' ? 'Baba' : ($student->pivot->relationship === 'guardian' ? 'Vasi' : 'Diğer')) }}
                                                @if($student->pivot->is_primary ?? false)
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">Birincil</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-10">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium">Henüz öğrenci atanmamış</p>
                        <p class="text-sm text-gray-400 mt-1">Öğrenci eklemek için veliyi düzenleyin.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Aidat bilgileri: veliye bağlı her öğrenci için ödenen / bekleyen / gecikmiş --}}
@if($parent->students->count() > 0)
<div class="mt-8 bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 flex items-center">
            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            Aidat Bilgileri
        </h3>
        <p class="text-sm text-gray-500 mt-1">Hangi öğrencinin aidatı ödendi / bekliyor / gecikmiş</p>
    </div>
    <div class="p-6">
        <div class="space-y-6">
            @foreach($parent->students as $student)
            @php
                $fees = $student->studentFees ?? collect();
                $paid = $fees->where('status', 'paid');
                $pending = $fees->where('status', 'pending');
                $overdue = $fees->where('status', 'overdue');
                $paidCount = $paid->count();
                $pendingCount = $pending->count();
                $overdueCount = $overdue->count();
                $paidTotal = $paid->sum('amount');
                $pendingTotal = $pending->sum('amount');
                $overdueTotal = $overdue->sum('amount');
            @endphp
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center justify-between flex-wrap gap-2">
                    <a href="{{ route('admin.students.show', $student) }}" class="font-semibold text-gray-900 hover:text-indigo-600 flex items-center">
                        <span class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 text-xs font-semibold mr-2">{{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}</span>
                        {{ $student->first_name }} {{ $student->last_name }}
                    </a>
                    <a href="{{ route('admin.student-fees.index') }}?student_id={{ $student->id }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Tüm aidatlar →</a>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-green-50 border border-green-100">
                            <span class="text-sm font-medium text-green-800">Ödendi</span>
                            <div class="text-right">
                                <span class="block font-semibold text-green-900">{{ $paidCount }} aidat</span>
                                <span class="text-xs text-green-700">{{ number_format($paidTotal, 2) }} ₺</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-yellow-50 border border-yellow-100">
                            <span class="text-sm font-medium text-yellow-800">Beklemede</span>
                            <div class="text-right">
                                <span class="block font-semibold text-yellow-900">{{ $pendingCount }} aidat</span>
                                <span class="text-xs text-yellow-700">{{ number_format($pendingTotal, 2) }} ₺</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-red-50 border border-red-100">
                            <span class="text-sm font-medium text-red-800">Gecikmiş</span>
                            <div class="text-right">
                                <span class="block font-semibold text-red-900">{{ $overdueCount }} aidat</span>
                                <span class="text-xs text-red-700">{{ number_format($overdueTotal, 2) }} ₺</span>
                            </div>
                        </div>
                    </div>
                    @if($fees->isEmpty())
                    <p class="text-sm text-gray-500 mt-3">Bu öğrenci için tanımlı aidat yok.</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
