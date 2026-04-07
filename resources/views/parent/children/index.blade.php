@extends('layouts.panel')

@section('title', 'Çocuklarım')
@section('page-title', 'Çocuklarım')

@section('sidebar-menu')
    @include('parent.partials.sidebar')
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @forelse($students as $student)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $student->first_name }} {{ $student->last_name }}</h3>
        <dl class="space-y-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">Sınıf</dt>
                <dd class="text-sm text-gray-900">{{ $student->classModel->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Okul</dt>
                <dd class="text-sm text-gray-900">{{ $student->school->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Doğum Tarihi</dt>
                <dd class="text-sm text-gray-900">{{ $student->birth_date ? $student->birth_date->format('d.m.Y') : '-' }}</dd>
            </div>
        </dl>
    </div>
    @empty
    <div class="col-span-2 bg-white rounded-lg shadow p-6 text-center text-gray-500">
        Henüz öğrenci kaydı bulunmamaktadır.
    </div>
    @endforelse
</div>
@endsection
