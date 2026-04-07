@extends('layouts.panel')

@section('title', 'Lisansı Biten Okullar')
@section('page-title', 'Lisansı Biten Okullar')
@section('page-description', 'Lisans süresi dolmuş okullar. Detaydan okul geçmişi ve lisans uzatımı yapabilirsiniz.')

@section('sidebar-menu')
@include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="mb-6">
    <a href="{{ route('superadmin.schools.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>Okullar sayfasına dön
    </a>
</div>

<div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
    <i class="fas fa-exclamation-triangle text-amber-600 text-xl mt-0.5"></i>
    <div>
        <h3 class="font-semibold text-amber-900">Bilgilendirme</h3>
        <p class="text-sm text-amber-800 mt-1">Bu listede lisans süresi dolmuş okullar yer alır. Okul detayından lisans uzatımı yapabilir veya iletişime geçebilirsiniz. Aktif lisanslı okullar <a href="{{ route('superadmin.schools.index') }}" class="underline font-medium">Okullar</a> sayfasında listelenir.</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
        <h3 class="text-lg font-semibold text-gray-800">Lisans süresi dolmuş okul listesi</h3>
        <p class="text-sm text-gray-500 mt-0.5">{{ $schools->total() }} okul</p>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Okul Adı</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">İletişim</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Lisans Türü</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Bitiş Tarihi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Kaç gün önce bitti</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Öğrenci / Sınıf</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($schools as $school)
                @php
                    $daysLeft = $school->getDaysUntilLicenseExpires();
                    $daysAgo = $daysLeft !== null && $daysLeft < 0 ? abs($daysLeft) : 0;
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        <div>{{ $school->email }}</div>
                        <div class="text-xs text-gray-500">{{ $school->phone ?? '—' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $licenseTypeBadge[0] }}">
                            <i class="fas {{ $licenseTypeBadge[1] }} mr-1.5"></i>{{ $school->license_type_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $school->demo_expires_at ? $school->demo_expires_at->format('d.m.Y') : '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">{{ $daysAgo }} gün önce</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $school->students_count }} öğrenci · {{ $school->classes_count }} sınıf</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <a href="{{ route('superadmin.schools.show', $school) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-eye mr-1.5"></i>Detay
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center text-gray-500">
                            <i class="fas fa-check-circle text-4xl text-green-300 mb-3"></i>
                            <p class="font-medium">Lisansı biten okul yok</p>
                            <p class="text-sm mt-1">Tüm okulların lisansı aktif.</p>
                            <a href="{{ route('superadmin.schools.index') }}" class="mt-3 text-indigo-600 hover:text-indigo-800 text-sm font-medium">Okullar sayfasına git →</a>
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
