@extends('layouts.panel')

@section('title', 'Yeni Mesaj')
@section('page-title', 'Yeni Mesaj')

@section('sidebar-menu')
    @include('parent.partials.sidebar')
@endsection

@section('content')
<div class="mb-6">
    <a href="{{ route('parent.messages.index') }}" class="text-indigo-600 hover:text-indigo-800 inline-flex items-center font-medium">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Mesajlara Dön
    </a>
</div>

<div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 max-w-2xl">
    <h2 class="text-lg font-bold text-gray-800 mb-4">Antrenöre Mesaj Gönder</h2>

    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>
    @endif

    @if($students->isEmpty())
    <p class="text-gray-500">Kayıtlı öğrenciniz veya aktif derse kayıtlı öğrenciniz bulunmuyor. Mesaj göndermek için öğrencinizin en az bir sınıfa kayıtlı olması gerekir.</p>
    @else
    @php
        $coachesByStudent = [];
        foreach ($students as $s) {
            $enrollments = $s->currentEnrollments ?? collect();
            $list = $enrollments->map(function($e) {
                $class = $e->classModel;
                $coach = $class?->coach;
                if (!$coach) return null;
                return ['id' => $coach->id, 'name' => $coach->user->name ?? 'Antrenör', 'class' => $class->name ?? '-'];
            })->filter()->unique('id')->values()->toArray();
            $coachesByStudent[$s->id] = $list;
        }
    @endphp
    <form action="{{ route('parent.messages.store') }}" method="POST">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Öğrenci</label>
                <select name="student_id" id="student_id" required class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Seçiniz</option>
                    @foreach($students as $s)
                    <option value="{{ $s->id }}" {{ old('student_id') == $s->id ? 'selected' : '' }}>{{ $s->first_name }} {{ $s->last_name }}</option>
                    @endforeach
                </select>
                @error('student_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="coach_id" class="block text-sm font-medium text-gray-700 mb-1">Antrenör</label>
                <select name="coach_id" id="coach_id" required class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Önce öğrenci seçin</option>
                </select>
                @error('coach_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Mesaj</label>
                <textarea name="body" id="body" rows="4" required maxlength="2000" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Mesajınızı yazın...">{{ old('body') }}</textarea>
                @error('body')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Gönder</button>
            <a href="{{ route('parent.messages.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300">İptal</a>
        </div>
    </form>

    <script>
    (function() {
        var coachesByStudent = @json($coachesByStudent);
        var studentSelect = document.getElementById('student_id');
        var coachSelect = document.getElementById('coach_id');
        var oldCoachId = {{ old('coach_id', 0) }};
        function updateCoaches() {
            var sid = studentSelect.value;
            coachSelect.innerHTML = '<option value="">Seçiniz</option>';
            if (!sid || !coachesByStudent[sid]) return;
            coachesByStudent[sid].forEach(function(c) {
                var opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.name + ' (' + c.class + ')';
                if (oldCoachId && c.id == oldCoachId) opt.selected = true;
                coachSelect.appendChild(opt);
            });
        }
        studentSelect.addEventListener('change', updateCoaches);
        updateCoaches();
    })();
    </script>
    @endif
</div>
@endsection
