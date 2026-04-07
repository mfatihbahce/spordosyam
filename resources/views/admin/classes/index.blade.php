@extends('layouts.panel')

@section('title', 'Sınıflar')
@section('page-title', 'Sınıflar')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4 flex justify-between items-center">
    <h3 class="text-lg font-semibold">Tüm Sınıflar</h3>
    <a href="{{ route('admin.classes.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
        + Yeni Sınıf Ekle
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sınıf Adı</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Branş</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Antrenör</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Öğrenci</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($classes as $class)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $class->name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $class->sportBranch->name ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $class->coach->user->name ?? '-' }}</td>
                @php
                    $isClassClosed = !$class->is_active || ($class->end_date && $class->end_date < now()->toDateString());
                    $displayStudentCount = $isClassClosed ? ($class->past_enrollments_count ?? 0) : ($class->current_enrollments_count ?? 0);
                @endphp
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $displayStudentCount }} / {{ $class->capacity }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @php
                        $isActuallyActive = $class->is_active && (!$class->end_date || $class->end_date >= now()->toDateString());
                    @endphp
                    <div class="flex flex-col gap-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $isActuallyActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $isActuallyActive ? 'Aktif' : 'Pasif' }}
                        </span>
                        @if($class->end_date)
                            <span class="text-xs text-gray-500">
                                @if($class->end_date < now())
                                    <span class="text-red-600">Bitiş: {{ $class->end_date->format('d.m.Y') }}</span>
                                @else
                                    Bitiş: {{ $class->end_date->format('d.m.Y') }}
                                @endif
                            </span>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('admin.classes.show', $class) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Görüntüle</a>
                    <a href="{{ route('admin.classes.edit', $class) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Düzenle</a>
                    <form action="{{ route('admin.classes.destroy', $class) }}" method="POST" class="inline" onsubmit="return confirm('Bu sınıfı silmek istediğinize emin misiniz?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Henüz sınıf bulunmamaktadır.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $classes->links() }}
    </div>
</div>
@endsection
