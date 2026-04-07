@extends('layouts.panel')

@section('title', 'Sınıf Detayı')
@section('page-title', $class->name)

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
@php
    $dayNames = [
        'monday' => ['tr' => 'Pazartesi', 'short' => 'Pzt'],
        'tuesday' => ['tr' => 'Salı', 'short' => 'Sal'],
        'wednesday' => ['tr' => 'Çarşamba', 'short' => 'Çar'],
        'thursday' => ['tr' => 'Perşembe', 'short' => 'Per'],
        'friday' => ['tr' => 'Cuma', 'short' => 'Cum'],
        'saturday' => ['tr' => 'Cumartesi', 'short' => 'Cmt'],
        'sunday' => ['tr' => 'Pazar', 'short' => 'Paz']
    ];
    $currentCount = isset($currentEnrollments) ? $currentEnrollments->count() : 0;
    $occupancy = $class->capacity > 0 ? round(($currentCount / $class->capacity) * 100, 1) : 0;
    // Kapalı sınıfta kartlarda bu sınıfta kayıtlı olmuş (mezun/ayrılmış) öğrenci sayısını göster
    $displayStudentCount = ($isClassClosed ?? false) && isset($pastEnrollments) ? $pastEnrollments->count() : $currentCount;
    $displayOccupancy = $class->capacity > 0 ? round(($displayStudentCount / $class->capacity) * 100, 1) : 0;
@endphp

<div class="mb-6">
    <a href="{{ route('admin.classes.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Geri Dön
    </a>
</div>

<!-- İstatistik Kartları: aktif sınıfta mevcut öğrenci, kapalı sınıfta bu sınıfta kayıtlı olmuş (mezun/ayrılmış) öğrenci sayısı -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-5 text-white">
        <div class="flex items-center justify-between mb-2">
            <div class="bg-white/20 rounded-lg p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>
        <div class="text-sm font-medium opacity-90">Toplam Öğrenci</div>
        <div class="text-3xl font-bold mt-1">{{ $displayStudentCount }}</div>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-5 text-white">
        <div class="flex items-center justify-between mb-2">
            <div class="bg-white/20 rounded-lg p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <div class="text-sm font-medium opacity-90">Kontenjan</div>
        <div class="text-3xl font-bold mt-1">{{ $class->capacity }}</div>
    </div>
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-5 text-white">
        <div class="flex items-center justify-between mb-2">
            <div class="bg-white/20 rounded-lg p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>
        <div class="text-sm font-medium opacity-90">Doluluk Oranı</div>
        <div class="text-3xl font-bold mt-1">{{ $displayOccupancy }}%</div>
    </div>
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-5 text-white">
        <div class="flex items-center justify-between mb-2">
            <div class="bg-white/20 rounded-lg p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
        <div class="text-sm font-medium opacity-90">Ders Günleri</div>
        <div class="text-3xl font-bold mt-1">{{ $class->class_days ? count($class->class_days) : 0 }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Sınıf Bilgileri -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">Sınıf Bilgileri</h3>
            <a href="{{ route('admin.classes.edit', $class) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Düzenle
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase">Sınıf Adı</p>
                    <p class="text-sm font-semibold text-gray-900 mt-1">{{ $class->name }}</p>
                </div>
            </div>

            <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase">Branş</p>
                    <p class="text-sm font-semibold text-gray-900 mt-1">{{ $class->sportBranch->name ?? '-' }}</p>
                </div>
            </div>

            <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase">Şube</p>
                    <p class="text-sm font-semibold text-gray-900 mt-1">{{ $class->branch->name ?? '-' }}</p>
                </div>
            </div>

            <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase">Antrenör</p>
                    <p class="text-sm font-semibold text-gray-900 mt-1">{{ $class->coach->user->name ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ders Programı -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Ders Programı</h3>
        @if($class->class_schedule && count($class->class_schedule) > 0)
            <div class="space-y-3">
                @foreach($class->class_schedule as $day => $schedule)
                    @php
                        $startTime = is_array($schedule) ? ($schedule['start_time'] ?? null) : $schedule;
                        $endTime = is_array($schedule) ? ($schedule['end_time'] ?? null) : null;
                    @endphp
                    @if($startTime)
                        <div class="flex items-center justify-between p-3 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-lg border border-indigo-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                    {{ $dayNames[$day]['short'] ?? substr($day, 0, 1) }}
                                </div>
                                <span class="text-sm font-semibold text-gray-800">{{ $dayNames[$day]['tr'] ?? $day }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-bold text-indigo-600">
                                    {{ $startTime }}@if($endTime) - {{ $endTime }}@endif
                                </span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            @if($class->end_date)
                <div class="mt-4 p-3 bg-{{ $class->end_date < now() ? 'red' : 'yellow' }}-50 border border-{{ $class->end_date < now() ? 'red' : 'yellow' }}-200 rounded-lg">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-{{ $class->end_date < now() ? 'red' : 'yellow' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-{{ $class->end_date < now() ? 'red' : 'yellow' }}-800">
                                Ders Bitiş Tarihi: {{ $class->end_date->format('d.m.Y') }}
                            </p>
                            @if($class->end_date < now())
                                <p class="text-xs text-red-600 mt-1">Bu sınıf bitiş tarihi geçtiği için kapalıdır.</p>
                            @else
                                <p class="text-xs text-yellow-600 mt-1">Bu tarihte sınıf otomatik olarak kapanacaktır.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @elseif($class->class_days && count($class->class_days) > 0)
            <div class="space-y-3">
                @foreach($class->class_days as $day)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-400 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                {{ $dayNames[$day]['short'] ?? substr($day, 0, 1) }}
                            </div>
                            <span class="text-sm font-semibold text-gray-800">{{ $dayNames[$day]['tr'] ?? $day }}</span>
                        </div>
                        <span class="text-sm text-gray-400">Saat belirtilmemiş</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-sm text-gray-500">Ders programı henüz belirlenmemiş</p>
            </div>
        @endif
    </div>
</div>

{{-- Öğrenciler bölümü sadece aktif sınıflarda; kapalı sınıfta sadece geçmiş kayıtlar listesi gösterilir --}}
@if(!($isClassClosed ?? false))
<!-- Öğrenciler -->
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <h3 class="text-xl font-bold text-gray-800">Öğrenciler ({{ $currentCount }})</h3>
        <div class="flex items-center gap-2 flex-wrap">
            @if($currentCount < $class->capacity)
                <span class="text-sm text-green-600 font-medium bg-green-50 px-3 py-1 rounded-full">
                    {{ $class->capacity - $currentCount }} kontenjan boş
                </span>
                <button type="button" id="openAddStudentsModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Öğrenci Ekle
                </button>
            @else
                <span class="text-sm text-red-600 font-medium bg-red-50 px-3 py-1 rounded-full">
                    Kontenjan dolu
                </span>
            @endif
        </div>
    </div>
    
    @if($currentCount > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Öğrenci</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">İletişim</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">E-posta</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($currentEnrollments ?? [] as $enrollment)
                    @php $student = $enrollment->student; @endphp
                    @if($student)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <span class="text-indigo-600 font-semibold text-sm">
                                        {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $student->first_name }} {{ $student->last_name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                {{ $student->phone ?? '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                {{ $student->email ?? '-' }}
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <p class="text-gray-500 font-medium">Bu sınıfta henüz öğrenci bulunmamaktadır</p>
            <p class="text-sm text-gray-400 mt-2 mb-4">Mevcut öğrencileri bu sınıfa eklemek için aşağıdaki butonu kullanın.</p>
            <button type="button" id="openAddStudentsModalEmpty" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Öğrenci Ekle
            </button>
        </div>
    @endif
</div>
@endif

{{-- Bu sınıfta kayıtlı olmuş öğrenciler (mezun / ayrıldı) --}}
@if(isset($pastEnrollments) && $pastEnrollments->count() > 0)
<div class="mt-6 bg-white rounded-xl shadow-md p-6">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Bu sınıfta kayıtlı olmuş öğrenciler</h3>
    <p class="text-sm text-gray-500 mb-4">Dersi bitiren veya başka sınıfa geçen öğrenciler</p>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Öğrenci</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kayıt</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ayrılış</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ders hakkı</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Durum</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($pastEnrollments as $h)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($h->student)
                        <a href="{{ route('admin.students.show', $h->student) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            {{ $h->student->first_name }} {{ $h->student->last_name }}
                        </a>
                        @else
                        <span class="text-sm text-gray-500">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $h->enrolled_at->format('d.m.Y') }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $h->left_at ? $h->left_at->format('d.m.Y') : '—' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $h->used_credits ?? 0 }} / {{ $h->total_credits ?? 8 }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($h->leave_reason === 'graduated' || (!$h->leave_reason && $class->end_date && \Carbon\Carbon::parse($class->end_date)->isPast()))
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Mezun</span>
                        @else
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">Ayrıldı</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Öğrenci ekleme popup --}}
<div id="addStudentsModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" id="addStudentsModalBackdrop"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[85vh] flex flex-col" role="dialog" aria-labelledby="addStudentsModalTitle" aria-modal="true">
            <div class="p-5 border-b border-gray-200 flex-shrink-0">
                <h3 id="addStudentsModalTitle" class="text-lg font-bold text-gray-900">Bu sınıfa öğrenci ekle</h3>
                <p class="text-sm text-gray-500 mt-0.5">{{ $class->name }}</p>
                <div class="mt-4">
                    <input type="text" id="addStudentsSearch" placeholder="Ad, soyad, telefon veya e-posta ile ara..." autocomplete="off"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            <div class="p-5 overflow-y-auto flex-1 min-h-0">
                <div id="addStudentsLoading" class="text-center py-8 text-gray-500 hidden">
                    <svg class="animate-spin h-8 w-8 mx-auto text-indigo-600 mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <p class="text-sm">Yükleniyor...</p>
                </div>
                <ul id="addStudentsList" class="divide-y divide-gray-100 space-y-0"></ul>
                <p id="addStudentsEmpty" class="text-sm text-gray-500 text-center py-4 hidden">Bu sınıfta olmayan öğrenci bulunamadı. Arama kriterinizi değiştirin.</p>
            </div>
            <form id="addStudentsForm" method="POST" action="{{ route('admin.classes.assign-students', $class) }}" class="p-5 border-t border-gray-200 flex-shrink-0 flex items-center justify-between gap-3">
                @csrf
                <div id="addStudentsHiddenWrap"></div>
                <button type="button" id="addStudentsModalClose" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">Kapat</button>
                <button type="submit" id="addStudentsSubmit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    Seçilenleri Derse Ekle (<span id="addStudentsCount">0</span>)
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var modal = document.getElementById('addStudentsModal');
    var backdrop = document.getElementById('addStudentsModalBackdrop');
    var searchInput = document.getElementById('addStudentsSearch');
    var listEl = document.getElementById('addStudentsList');
    var loadingEl = document.getElementById('addStudentsLoading');
    var emptyEl = document.getElementById('addStudentsEmpty');
    var form = document.getElementById('addStudentsForm');
    var hiddenWrap = document.getElementById('addStudentsHiddenWrap');
    var countEl = document.getElementById('addStudentsCount');
    var submitBtn = document.getElementById('addStudentsSubmit');
    var closeBtn = document.getElementById('addStudentsModalClose');
    var fetchTimeout = null;
    var selectedIds = new Set();
    var studentsData = [];

    var url = '{{ route("admin.classes.students-to-add", $class) }}';

    function openModal() {
        modal.classList.remove('hidden');
        selectedIds.clear();
        studentsData = [];
        searchInput.value = '';
        loadStudents();
    }
    function closeModal() {
        modal.classList.add('hidden');
    }
    function loadStudents(search) {
        loadingEl.classList.remove('hidden');
        listEl.innerHTML = '';
        emptyEl.classList.add('hidden');
        var u = url + (search ? '?search=' + encodeURIComponent(search) : '');
        fetch(u, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                loadingEl.classList.add('hidden');
                studentsData = data.students || [];
                renderList();
                updateSubmitState();
            })
            .catch(function() {
                loadingEl.classList.add('hidden');
                emptyEl.classList.remove('hidden');
                emptyEl.textContent = 'Yüklenirken hata oluştu.';
            });
    }
    function renderList() {
        listEl.innerHTML = '';
        if (studentsData.length === 0) {
            emptyEl.classList.remove('hidden');
            return;
        }
        emptyEl.classList.add('hidden');
        studentsData.forEach(function(s) {
            var li = document.createElement('li');
            li.className = 'flex items-center gap-3 py-3 hover:bg-gray-50 rounded-lg px-2 -mx-2';
            var checked = selectedIds.has(s.id) ? 'checked' : '';
            li.innerHTML = '<label class="flex items-center gap-3 flex-1 cursor-pointer">' +
                '<input type="checkbox" class="student-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" data-id="' + s.id + '">' +
                '<div class="flex-1 min-w-0">' +
                '<span class="font-medium text-gray-900 block truncate">' + (s.name || '-') + '</span>' +
                '<span class="text-xs text-gray-500 block truncate">' + (s.phone || '') + ' · ' + (s.email || '') + '</span>' +
                '</div></label>';
            listEl.appendChild(li);
            var cb = li.querySelector('.student-checkbox');
            cb.checked = selectedIds.has(s.id);
            cb.addEventListener('change', function() {
                if (this.checked) selectedIds.add(s.id); else selectedIds.delete(s.id);
                updateSubmitState();
            });
        });
    }
    function updateSubmitState() {
        var n = selectedIds.size;
        countEl.textContent = n;
        submitBtn.disabled = n === 0;
        var arr = Array.from(selectedIds);
        hiddenWrap.innerHTML = '';
        arr.forEach(function(id) {
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'student_ids[]';
            inp.value = id;
            hiddenWrap.appendChild(inp);
        });
    }
    document.getElementById('openAddStudentsModal').addEventListener('click', openModal);
    if (document.getElementById('openAddStudentsModalEmpty')) {
        document.getElementById('openAddStudentsModalEmpty').addEventListener('click', openModal);
    }
    closeBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);
    searchInput.addEventListener('input', function() {
        clearTimeout(fetchTimeout);
        var q = this.value.trim();
        fetchTimeout = setTimeout(function() { loadStudents(q); }, 300);
    });
    form.addEventListener('submit', function() {
        if (selectedIds.size === 0) return false;
        return true;
    });
})();
</script>
@endpush
@endsection
