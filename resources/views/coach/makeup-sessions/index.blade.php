@extends('layouts.panel')

@section('title', 'Telafi Derslerim')
@section('page-title', 'Telafi Derslerim')

@section('sidebar-menu')
@include('coach.partials.sidebar')
@endsection

@section('content')
<div class="mb-4">
    <p class="text-sm text-gray-500">Size atanan telafi dersleri aşağıda listelenir. Takvimde de görüntüleyebilirsiniz.</p>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih / Saat</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Şube</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Öğrenci Sayısı</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($sessions as $session)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $session->scheduled_date->format('d.m.Y') }}
                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 mr-2">Telafi</span>
                    {{ $session->name ?? 'Telafi Dersi' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $session->branch->name ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $session->student_makeup_classes_count }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                    Size atanan telafi dersi bulunmamaktadır.
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
