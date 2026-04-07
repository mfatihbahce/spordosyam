@extends('layouts.panel')

@section('title', 'Öğrenciler')
@section('page-title', 'Öğrenciler')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4 flex justify-between items-center flex-wrap gap-3">
    <h3 class="text-lg font-semibold">Tüm Öğrenciler</h3>
    <a href="{{ route('admin.students.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
        + Yeni Öğrenci Ekle
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form action="{{ route('admin.students.index') }}" method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label for="tc" class="block text-xs font-medium text-gray-500 mb-1">TC Kimlik No</label>
            <input type="text" name="tc" id="tc" maxlength="11" value="{{ request('tc') }}"
                   class="block w-full sm:w-36 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border"
                   placeholder="11 haneli">
        </div>
        <div>
            <label for="parent_name" class="block text-xs font-medium text-gray-500 mb-1">Veli Ad Soyad</label>
            <input type="text" name="parent_name" id="parent_name" value="{{ request('parent_name') }}"
                   class="block w-full sm:w-48 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border"
                   placeholder="Veli adı ile ara">
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium">Ara</button>
        @if(request()->hasAny(['tc', 'parent_name']))
        <a href="{{ route('admin.students.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 text-sm">Temizle</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ad Soyad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">TC Kimlik No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sınıf</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefon</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($students as $student)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->identity_number ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->classModel->name ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->phone ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->email ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @php $eff = $student->effective_is_active; @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $eff ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}" title="{{ !$eff && $student->is_active && $student->classModel && !($student->classModel->is_actually_active ?? true) ? 'Sınıf kapalı' : '' }}">
                        {{ $eff ? 'Aktif' : 'Pasif' }}
                    </span>
                    @if(!$eff && $student->is_active && $student->classModel && !($student->classModel->is_actually_active ?? true))
                        <span class="ml-1 text-xs text-amber-600" title="Sınıf kapalı">(Sınıf kapalı)</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('admin.students.show', $student) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Görüntüle</a>
                    <a href="{{ route('admin.students.edit', $student) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Düzenle</a>
                    <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="inline" onsubmit="return confirm('Bu öğrenciyi silmek istediğinize emin misiniz?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Henüz öğrenci bulunmamaktadır.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $students->links() }}
    </div>
</div>
@endsection
