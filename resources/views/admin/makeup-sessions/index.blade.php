@extends('layouts.panel')

@section('title', 'Telafi Dersleri')
@section('page-title', 'Telafi Dersleri')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4 flex justify-between items-center">
    <p class="text-sm text-gray-500">Boş bir güne ve saate telafi dersi ekleyin; ardından telafi bekleyen öğrencileri bu derse ekleyin.</p>
    <a href="{{ route('admin.makeup-sessions.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
        <i class="fas fa-plus mr-2"></i>Telafi Dersi Ekle
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih / Saat</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Antrenör</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Şube</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Öğrenci Sayısı</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($sessions as $session)
            @php
                $startTimeStr = $session->start_time instanceof \DateTimeInterface ? $session->start_time->format('H:i') : \Carbon\Carbon::parse($session->start_time)->format('H:i');
                $sessionStart = \Carbon\Carbon::parse($session->scheduled_date->format('Y-m-d') . ' ' . $startTimeStr);
                $hasStarted = now()->gte($sessionStart);
            @endphp
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 mr-2">Telafi</span>
                    <span class="text-sm font-medium text-gray-900">{{ $session->name ?? 'Telafi Dersi' }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $session->scheduled_date->format('d.m.Y') }}
                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $session->coach->user->name ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $session->branch->name ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $session->student_makeup_classes_count }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('admin.makeup-sessions.show', $session) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detay</a>
                    @if(!$hasStarted)
                    <a href="{{ route('admin.makeup-sessions.edit', $session) }}" class="text-gray-600 hover:text-gray-900">Düzenle</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                    Henüz telafi dersi eklenmemiş. "Telafi Dersi Ekle" ile boş bir güne ve saate telafi dersi açabilirsiniz.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($sessions->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $sessions->links() }}
    </div>
    @endif
</div>
@endsection
