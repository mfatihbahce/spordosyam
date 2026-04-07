@extends('layouts.panel')

@section('title', 'Şube Detayı')
@section('page-title', $branch->name)

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.branches.index') }}" class="text-indigo-600 hover:text-indigo-900">← Geri Dön</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Şube Bilgileri</h3>
        <dl class="space-y-3">
            <div>
                <dt class="text-sm font-medium text-gray-500">Şube Adı</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $branch->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Adres</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $branch->address ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Telefon</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $branch->phone ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Durum</dt>
                <dd class="mt-1">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $branch->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $branch->is_active ? 'Aktif' : 'Pasif' }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">İstatistikler</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="rounded-xl bg-indigo-50 border border-indigo-100 p-4">
                <dt class="text-xs font-medium text-indigo-600 uppercase">Toplam Sınıf</dt>
                <dd class="mt-1 text-2xl font-bold text-indigo-900">{{ $branch->classes->count() }}</dd>
            </div>
            <div class="rounded-xl bg-blue-50 border border-blue-100 p-4">
                <dt class="text-xs font-medium text-blue-600 uppercase">Öğrenci</dt>
                <dd class="mt-1 text-2xl font-bold text-blue-900">{{ $studentsCount }}</dd>
            </div>
            <div class="rounded-xl bg-amber-50 border border-amber-100 p-4">
                <dt class="text-xs font-medium text-amber-600 uppercase">Antrenör</dt>
                <dd class="mt-1 text-2xl font-bold text-amber-900">{{ $coachesCount }}</dd>
            </div>
            <div class="rounded-xl bg-green-50 border border-green-100 p-4">
                <dt class="text-xs font-medium text-green-600 uppercase">Tahsil Edilen Aidat</dt>
                <dd class="mt-1 text-2xl font-bold text-green-900">{{ number_format($totalDuesCollected, 2) }} ₺</dd>
            </div>
            <div class="col-span-2 rounded-xl bg-gray-50 border border-gray-200 p-4">
                <dt class="text-xs font-medium text-gray-600 uppercase">Bekleyen Aidat</dt>
                <dd class="mt-1 flex items-baseline gap-2">
                    <span class="text-xl font-bold text-gray-900">{{ number_format($pendingDuesAmount, 2) }} ₺</span>
                    <span class="text-sm text-gray-500">({{ $pendingDuesCount }} adet)</span>
                </dd>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <h3 class="text-lg font-semibold p-6 pb-0">Şubedeki Sınıflar</h3>
    <div class="p-6">
        @if($branch->classes->count() > 0)
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sınıf</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Branş</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Antrenör</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Öğrenci Sayısı</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($branch->classes as $class)
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $class->name }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $class->sportBranch->name ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $class->coach->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $class->students_count ?? 0 }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @php $active = $class->is_active ?? false; @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $active ? 'Aktif' : 'Pasif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                        <a href="{{ route('admin.classes.show', $class) }}" class="text-indigo-600 hover:text-indigo-900">Detay</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-gray-500 text-sm">Bu şubede henüz sınıf bulunmuyor.</p>
        @endif
    </div>
</div>
@endsection
