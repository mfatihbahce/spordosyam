@extends('layouts.panel')

@section('title', 'Öğrenci Detayı')
@section('page-title', $student->first_name . ' ' . $student->last_name)

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.students.index') }}" class="text-indigo-600 hover:text-indigo-900 inline-flex items-center">
        <i class="fas fa-arrow-left mr-1.5 text-sm"></i> Geri Dön
    </a>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.students.edit', $student) }}" class="px-3 py-1.5 bg-amber-100 text-amber-800 rounded-lg text-sm font-medium hover:bg-amber-200">
            <i class="fas fa-edit mr-1"></i> Düzenle
        </a>
    </div>
</div>

@php
    $effectiveActive = $student->effective_is_active;
    $classActive = $student->classModel ? ($student->classModel->is_actually_active ?? true) : false;
@endphp

{{-- Durum kartı --}}
<div class="mb-6 flex flex-wrap gap-3">
    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $effectiveActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
        <i class="fas {{ $effectiveActive ? 'fa-check-circle' : 'fa-times-circle' }} mr-1.5"></i>
        {{ $effectiveActive ? 'Aktif' : 'Pasif' }}
    </span>
    @if(!$effectiveActive && $student->is_active && $student->classModel && !$classActive)
        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
            <i class="fas fa-info-circle mr-1.5"></i> Sınıf kapalı olduğu için pasif gösteriliyor
        </span>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Sol: Öğrenci bilgileri --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-5 py-3 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-user text-indigo-600 mr-2 text-sm"></i>
                    Öğrenci Bilgileri
                </h3>
            </div>
            <div class="p-5">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ad Soyad</dt>
                        <dd class="mt-0.5 text-sm font-medium text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">TC Kimlik No</dt>
                        <dd class="mt-0.5 text-sm text-gray-900">{{ $student->identity_number ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Doğum Tarihi</dt>
                        <dd class="mt-0.5 text-sm text-gray-900">{{ $student->birth_date ? $student->birth_date->format('d.m.Y') : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Cinsiyet</dt>
                        <dd class="mt-0.5 text-sm text-gray-900">{{ $student->gender === 'male' ? 'Erkek' : ($student->gender === 'female' ? 'Kız' : '-') }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Mevcut Sınıf</dt>
                        <dd class="mt-0.5 flex items-center gap-2">
                            @if($student->classModel)
                                <a href="{{ route('admin.classes.show', $student->classModel) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">{{ $student->classModel->name }}</a>
                                <span class="px-2 py-0.5 rounded text-xs font-medium {{ $classActive ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $classActive ? 'Sınıf aktif' : 'Sınıf kapalı' }}
                                </span>
                            @else
                                <span class="text-sm text-gray-500">Sınıf atanmamış</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Telefon</dt>
                        <dd class="mt-0.5 text-sm text-gray-900">{{ $student->phone ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">E-posta</dt>
                        <dd class="mt-0.5 text-sm text-gray-900">{{ $student->email ?: '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Adres</dt>
                        <dd class="mt-0.5 text-sm text-gray-900">{{ $student->address ?: '-' }}</dd>
                    </div>
                    @if($student->notes)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Notlar</dt>
                        <dd class="mt-0.5 text-sm text-gray-700 whitespace-pre-wrap">{{ $student->notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Veliler --}}
        @if($student->parents->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-5 py-3 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-users text-slate-600 mr-2 text-sm"></i>
                    Veliler
                </h3>
            </div>
            <div class="p-5">
                <ul class="space-y-3">
                    @foreach($student->parents as $parent)
                    <li class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div>
                            <span class="font-medium text-gray-900">{{ $parent->user->name ?? '-' }}</span>
                            @if($parent->pivot->relationship)
                                <span class="text-xs text-gray-500 ml-2">({{ $parent->pivot->relationship }})</span>
                            @endif
                            @if($parent->pivot->is_primary ?? false)
                                <span class="ml-2 px-1.5 py-0.5 bg-indigo-50 text-indigo-700 text-xs rounded">Birincil</span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-500">{{ $parent->phone ?? $parent->user->email ?? '-' }}</div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>

    {{-- Sağ: İstatistikler --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-chart-bar text-indigo-600 mr-2 text-sm"></i>
                İstatistikler
            </h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 rounded-lg bg-indigo-50 border border-indigo-100">
                    <span class="text-sm font-medium text-indigo-800">Toplam Yoklama</span>
                    <span class="text-xl font-bold text-indigo-900">{{ $student->attendances->count() }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-green-50 border border-green-100">
                    <span class="text-sm font-medium text-green-800">Derste Var</span>
                    <span class="text-xl font-bold text-green-900">{{ $student->attendances->where('status', 'present')->count() }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-red-50 border border-red-100">
                    <span class="text-sm font-medium text-red-800">Bekleyen Aidat</span>
                    <span class="text-xl font-bold text-red-900">₺{{ number_format($student->studentFees->where('status', 'pending')->sum('amount'), 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Aidat Ekle + Bu öğrencinin aidat listesi --}}
<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-5 py-3 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-plus-circle text-emerald-600 mr-2 text-sm"></i>
                    Aidat Ekle
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Öğrenciye tutar tanımlayın, veli panelden ödeyebilir.</p>
            </div>
            <div class="p-5">
                <form action="{{ route('admin.students.store-aidat', $student) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Tutar (₺)</label>
                        <input type="number" step="0.01" min="0.01" name="amount" id="amount" required
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                               placeholder="0.00" value="{{ old('amount') }}">
                        @error('amount')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Vade Tarihi</label>
                        <input type="date" name="due_date" id="due_date" required
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                               value="{{ old('due_date', now()->endOfMonth()->format('Y-m-d')) }}">
                        @error('due_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Not (isteğe bağlı)</label>
                        <input type="text" name="notes" id="notes" maxlength="500"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                               placeholder="Örn: Şubat aidatı" value="{{ old('notes') }}">
                        @error('notes')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        <i class="fas fa-save mr-2"></i> Aidat Kaydet
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-5 py-3 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-list text-slate-600 mr-2 text-sm"></i>
                    Bu öğrencinin aidatları
                </h3>
            </div>
            <div class="overflow-x-auto">
                @if($student->studentFees->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutar</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vade</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Not</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($student->studentFees->sortByDesc('due_date') as $sf)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">₺{{ number_format($sf->amount, 2) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $sf->due_date->format('d.m.Y') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $statusColors = ['pending' => 'bg-yellow-100 text-yellow-800', 'paid' => 'bg-green-100 text-green-800', 'overdue' => 'bg-red-100 text-red-800', 'cancelled' => 'bg-gray-100 text-gray-700'];
                                    $statusLabels = ['pending' => 'Beklemede', 'paid' => 'Ödendi', 'overdue' => 'Gecikmiş', 'cancelled' => 'İptal'];
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$sf->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $statusLabels[$sf->status] ?? $sf->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $sf->notes ?: '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="p-8 text-center text-gray-500 text-sm">
                    <i class="fas fa-coins text-gray-300 text-2xl mb-2 block"></i>
                    Henüz aidat tanımlanmamış. Yukarıdaki formdan ekleyebilirsiniz.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Ders geçmişi (aldığı / aldığı dersler, aktif-pasif) --}}
<div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-5 py-3 border-b border-gray-200">
        <h3 class="text-base font-semibold text-gray-900 flex items-center">
            <i class="fas fa-history text-indigo-600 mr-2 text-sm"></i>
            Ders / Sınıf Geçmişi
        </h3>
        <p class="text-xs text-gray-500 mt-1">Öğrencinin kayıtlı olduğu sınıflar ve durumları</p>
    </div>
    <div class="overflow-x-auto">
        @if($student->classHistory && $student->classHistory->count() > 0)
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sınıf</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Branş</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Şube</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kayıt</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ayrılış</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ders hakkı</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($student->classHistory as $h)
                @php
                    $cls = $h->classModel;
                    $isCurrent = is_null($h->left_at);
                    $classActuallyActive = $cls ? ($cls->is_actually_active ?? ($cls->is_active && (!$cls->end_date || $cls->end_date >= now()->toDateString()))) : false;
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($cls)
                            <a href="{{ route('admin.classes.show', $cls) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">{{ $cls->name }}</a>
                        @else
                            <span class="text-sm text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $cls->sportBranch->name ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $cls->branch->name ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $h->enrolled_at->format('d.m.Y') }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $h->left_at ? $h->left_at->format('d.m.Y') : '—' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $h->used_credits ?? 0 }} / {{ $h->total_credits ?? 8 }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($isCurrent)
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $classActuallyActive ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $classActuallyActive ? 'Mevcut' : 'Sınıf kapalı' }}
                            </span>
                        @elseif($h->leave_reason === 'graduated')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Mezun</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">Ayrıldı</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-8 text-center text-gray-500 text-sm">
            <i class="fas fa-info-circle text-gray-400 text-2xl mb-2 block"></i>
            Henüz sınıf geçmişi kaydı bulunmuyor.
        </div>
        @endif
    </div>
</div>
@endsection
