@extends('layouts.panel')

@section('title', 'Gelişim Notu Detayı')
@section('page-title', 'Gelişim Notu Detayı')

@section('sidebar-menu')
    @include('parent.partials.sidebar')
@endsection

@section('content')
@php
    $typeLabels = [
        'skill' => 'Teknik Beceri',
        'attitude' => 'Davranış',
        'physical' => 'Fiziksel Gelişim',
        'general' => 'Genel',
    ];
    $typeLabel = $typeLabels[$progress->progress_type ?? 'general'] ?? 'Genel';
@endphp
<div class="max-w-3xl mx-auto">
    {{-- Geri butonu --}}
    <a href="{{ route('parent.progress.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-violet-600 transition-colors mb-6 group">
        <i class="fas fa-arrow-left text-sm group-hover:-translate-x-0.5 transition-transform"></i>
        <span class="text-sm font-medium">Gelişim Notlarına Dön</span>
    </a>

    {{-- Hero kart --}}
    <div class="bg-gradient-to-br from-violet-500 via-violet-600 to-indigo-700 rounded-2xl p-6 mb-6 shadow-lg shadow-violet-500/20">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <i class="fas fa-chart-line text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white">
                        {{ $progress->student->first_name ?? '' }} {{ $progress->student->last_name ?? '' }}
                    </h1>
                    <p class="text-violet-100 text-sm mt-0.5">{{ $progress->classModel->name ?? '-' }}</p>
                    <span class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 rounded-full bg-white/20 text-white text-xs font-medium">
                        {{ $typeLabel }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <p class="text-violet-100 text-xs uppercase tracking-wider">Tarih</p>
                <p class="text-white font-semibold">{{ $progress->progress_date->format('d.m.Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Bilgi kartları --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-all hover:border-violet-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Öğrenci</p>
                    <p class="font-semibold text-slate-800">{{ $progress->student->first_name ?? '' }} {{ $progress->student->last_name ?? '' }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-all hover:border-violet-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Sınıf</p>
                    <p class="font-semibold text-slate-800">{{ $progress->classModel->name ?? '-' }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-all hover:border-violet-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-tag"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Tür</p>
                    <p class="font-semibold text-slate-800">{{ $typeLabel }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-all hover:border-violet-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Antrenör</p>
                    <p class="font-semibold text-slate-800">{{ $progress->coach && $progress->coach->user ? $progress->coach->user->name : '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Gelişim notu içeriği --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center text-violet-600">
                <i class="fas fa-sticky-note text-sm"></i>
            </div>
            <div>
                <h3 class="text-base font-semibold text-slate-800">Gelişim Notu</h3>
                <p class="text-xs text-slate-500">Antrenörünüzün değerlendirmesi</p>
            </div>
        </div>
        <div class="p-6">
            <div class="prose prose-slate max-w-none">
                <p class="text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $progress->notes ?? 'Henüz not eklenmemiş.' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
