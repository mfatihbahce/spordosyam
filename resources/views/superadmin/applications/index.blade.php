@extends('layouts.panel')

@section('title', 'Bekleyen Başvurular')
@section('page-title', 'Bekleyen Başvurular')
@section('page-description', 'Onay veya red bekleyen demo talepleri. Onaylanan okullar Okullar sayfasında listelenir.')

@section('sidebar-menu')
@include('superadmin.partials.sidebar')
@endsection

@section('content')
@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-center">
        <i class="fas fa-check-circle mr-2 text-green-600"></i>{{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Talep listesi</h3>
            <p class="text-sm text-gray-500 mt-0.5">{{ $applications->total() }} bekleyen başvuru</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Okul Adı</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">İletişim</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Telefon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Tarih</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($applications as $application)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $application->school_name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $application->contact_name }} · {{ $application->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $application->phone }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $application->created_at->format('d.m.Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <a href="{{ route('superadmin.applications.show', $application) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-eye mr-1.5"></i>Detay
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                            <p class="font-medium">Bekleyen başvuru yok</p>
                            <p class="text-sm mt-1">Yeni demo talepleri burada listelenecek.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($applications->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
        {{ $applications->links() }}
    </div>
    @endif
</div>
@endsection
