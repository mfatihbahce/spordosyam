@extends('layouts.panel')

@section('title', 'Telafi Dersi Düzenle')
@section('page-title', 'Telafi Dersi Düzenle')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.makeup-sessions.show', $makeupSession) }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>
        Geri Dön
    </a>
</div>

@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
        <h2 class="text-xl font-bold text-white">Telafi Dersi Düzenle</h2>
    </div>

    <form action="{{ route('admin.makeup-sessions.update', $makeupSession) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Tarih *</label>
                <input type="date" name="scheduled_date" id="scheduled_date" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                       value="{{ old('scheduled_date', $makeupSession->scheduled_date->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Başlangıç saati *</label>
                    <input type="time" name="start_time" id="start_time" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('start_time', \Carbon\Carbon::parse($makeupSession->start_time)->format('H:i')) }}">
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">Bitiş saati *</label>
                    <input type="time" name="end_time" id="end_time" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('end_time', \Carbon\Carbon::parse($makeupSession->end_time)->format('H:i')) }}">
                </div>
            </div>
            <div>
                <label for="coach_id" class="block text-sm font-medium text-gray-700 mb-2">Antrenör *</label>
                <select name="coach_id" id="coach_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($coaches as $coach)
                        <option value="{{ $coach->id }}" {{ old('coach_id', $makeupSession->coach_id) == $coach->id ? 'selected' : '' }}>
                            {{ $coach->user->name ?? $coach->id }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-2">Şube (opsiyonel)</label>
                <select name="branch_id" id="branch_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Şube seçin</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id', $makeupSession->branch_id) == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Ad (opsiyonel)</label>
                <input type="text" name="name" id="name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                       value="{{ old('name', $makeupSession->name) }}">
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.makeup-sessions.show', $makeupSession) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">İptal</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <i class="fas fa-save mr-2"></i>Kaydet
            </button>
        </div>
    </form>
</div>
@endsection
