@extends('layouts.panel')

@section('title', 'Sınıflarım')
@section('page-title', 'Sınıflarım')

@section('sidebar-menu')
    @include('coach.partials.sidebar')
@endsection

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Sınıflarım</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sınıf Adı</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Branş</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Öğrenci Sayısı</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kapasite</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($classes as $class)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $class->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $class->sportBranch->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $class->students_count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $class->capacity }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Henüz sınıf bulunmamaktadır.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $classes->links() }}
    </div>
</div>
@endsection
