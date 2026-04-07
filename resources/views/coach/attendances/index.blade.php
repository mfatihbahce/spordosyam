@extends('layouts.panel')

@section('title', 'Yoklama Geçmişi')
@section('page-title', 'Yoklama Geçmişi')

@section('sidebar-menu')
@include('coach.partials.sidebar')
@endsection

@section('content')
<div class="mb-4 flex justify-between items-center">
    <h3 class="text-lg font-semibold">Yoklama Geçmişi</h3>
    <a href="{{ route('coach.attendances.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
        + Yeni Yoklama Al
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Öğrenci</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sınıf</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Not</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($attendances as $attendance)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $attendance->student->first_name }} {{ $attendance->student->last_name }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    @if($attendance->makeupSession)
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Telafi</span>
                        {{ $attendance->makeupSession->name ?? 'Telafi Dersi' }}
                    @else
                        {{ $attendance->classModel->name ?? '-' }}
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attendance->attendance_date->format('d.m.Y') }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $attendance->status === 'present' ? 'bg-green-100 text-green-800' : ($attendance->status === 'excused' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ $attendance->status === 'present' ? 'Mevcut' : ($attendance->status === 'excused' ? 'İzinli' : 'Yok') }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $attendance->notes ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Henüz yoklama kaydı bulunmamaktadır.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $attendances->links() }}
    </div>
</div>
@endsection
