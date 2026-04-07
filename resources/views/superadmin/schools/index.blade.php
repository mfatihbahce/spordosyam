@extends('layouts.panel')

@section('title', 'Aktif Lisanslı Okullar')
@section('page-title', 'Spor Okulları')
@section('page-description', 'Aktif lisansı olan okullar. Lisansı bitenler "Lisansı Biten Okullar" menüsünden görüntülenir.')

@section('sidebar-menu')
@include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h3 class="text-lg font-semibold text-gray-800">Aktif Lisanslı Okullar</h3>
        <p class="text-sm text-gray-500 mt-0.5">{{ $schools->total() }} okul</p>
    </div>
    <a href="{{ route('superadmin.schools.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
        <i class="fas fa-plus mr-2"></i>Yeni Okul Ekle
    </a>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-center">
        <i class="fas fa-check-circle mr-2 text-green-600"></i>{{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Okul Adı</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Telefon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Öğrenci</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Lisans Türü</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Bitiş / Kalan Gün</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Durum</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($schools as $school)
                @php
                    $daysLeft = $school->getDaysUntilLicenseExpires();
                    $licenseTypeBadge = match($school->license_type) {
                        'demo' => ['bg-indigo-100 text-indigo-800', 'fa-vial'],
                        'free' => ['bg-green-100 text-green-800', 'fa-gift'],
                        'paid' => ['bg-amber-100 text-amber-800', 'fa-lira-sign'],
                        default => ['bg-gray-100 text-gray-800', 'fa-key'],
                    };
                @endphp
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $school->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $school->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $school->phone ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $school->students_count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $licenseTypeBadge[0] }}">
                            <i class="fas {{ $licenseTypeBadge[1] }} mr-1.5"></i>{{ $school->license_type_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        @if($school->demo_expires_at)
                            <span class="text-gray-600">{{ $school->demo_expires_at->format('d.m.Y') }}</span>
                            @if($daysLeft !== null)
                                <span class="block text-xs {{ $daysLeft <= 10 ? 'text-amber-600 font-medium' : 'text-gray-500' }}">
                                    {{ $daysLeft >= 0 ? $daysLeft . ' gün kaldı' : '—' }}
                                </span>
                            @endif
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($school->is_active)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Pasif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('superadmin.schools.show', $school) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Görüntüle</a>
                        <a href="{{ route('superadmin.schools.edit', $school) }}" class="text-amber-600 hover:text-amber-900 mr-3">Düzenle</a>
                        <form action="{{ route('superadmin.schools.destroy', $school) }}" method="POST" class="inline" onsubmit="return confirm('Bu okulu silmek istediğinize emin misiniz?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center text-gray-500">
                            <i class="fas fa-school text-4xl text-gray-300 mb-3"></i>
                            <p class="font-medium">Aktif lisanslı okul yok</p>
                            <p class="text-sm mt-1">Lisansı biten okullar "Lisansı Biten Okullar" sayfasında listelenir.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($schools->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
        {{ $schools->links() }}
    </div>
    @endif
</div>
@endsection
