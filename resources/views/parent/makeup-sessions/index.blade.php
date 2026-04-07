@extends('layouts.panel')

@section('title', 'Telafi Dersleri')
@section('page-title', 'Telafi Dersleri')

@section('sidebar-menu')
@include('parent.partials.sidebar')
@endsection

@section('content')
<div class="mb-4">
    <p class="text-sm text-gray-500">Çocuğunuzun planlanmış telafi dersleri aşağıda listelenir. Takvimde de görüntüleyebilirsiniz.</p>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Öğrenci</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih / Saat</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telafi Dersi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Antrenör</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Şube</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($makeups as $sm)
            @php($session = $sm->makeupSession)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $sm->student->first_name }} {{ $sm->student->last_name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $session->scheduled_date->format('d.m.Y') }}
                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Telafi</span>
                    {{ $session->name ?? 'Telafi Dersi' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $session->coach->user->name ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $session->branch->name ?? '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                    Planlanmış telafi dersi bulunmamaktadır.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($makeups->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $makeups->links() }}
    </div>
    @endif
</div>
@endsection
