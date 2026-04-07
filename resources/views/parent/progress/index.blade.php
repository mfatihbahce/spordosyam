@extends('layouts.panel')

@section('title', 'Gelişim Notları')
@section('page-title', 'Gelişim Notları')

@section('sidebar-menu')
    @include('parent.partials.sidebar')
@endsection

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Gelişim Notları</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Öğrenci</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sınıf</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notlar</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($progresses as $progress)
                <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('parent.progress.show', $progress) }}'">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $progress->student->first_name }} {{ $progress->student->last_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $progress->classModel->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $progress->progress_date->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($progress->notes, 80) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Henüz gelişim notu bulunmamaktadır.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $progresses->links() }}
    </div>
</div>
@endsection
