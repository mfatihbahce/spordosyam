@extends('layouts.panel')

@section('title', 'Veli Düzenle')
@section('page-title', $parent->user->name . ' - Düzenle')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.parents.update', $parent) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Ad Soyad *</label>
                <input type="text" name="name" id="name" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border"
                       value="{{ old('name', $parent->user->name) }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                <input type="email" name="email" id="email" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border"
                       value="{{ old('email', $parent->user->email) }}">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Telefon</label>
                <input type="text" name="phone" id="phone"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border"
                       value="{{ old('phone', $parent->phone) }}">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700">Adres</label>
                <textarea name="address" id="address" rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border">{{ old('address', $parent->address) }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Öğrenciler (TC ile ekleyin)</label>
                <div class="flex flex-wrap gap-2 mb-3">
                    <input type="text" id="student_tc_input" maxlength="11" placeholder="11 haneli TC Kimlik No"
                           class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border w-40">
                    <button type="button" id="add_student_by_tc_btn" class="px-3 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Ekle</button>
                </div>
                <p id="student_tc_message" class="text-sm text-red-600 hidden mb-2"></p>
                <div id="parent_students_list" class="border rounded-lg p-4 max-h-60 overflow-y-auto space-y-2">
                    <p id="no_students_hint" class="text-sm text-gray-500 {{ $parent->students->isEmpty() ? '' : 'hidden' }}">TC kimlik no girip Ekle ile veliye öğrenci ekleyin.</p>
                    @foreach($parent->students as $student)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0" data-student-id="{{ $student->id }}">
                        <span class="text-sm font-medium text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</span>
                        <div class="flex items-center gap-2">
                            <select name="relationships[{{ $student->id }}]" class="text-sm border rounded px-2 py-1">
                                @php $rel = old("relationships.{$student->id}", $student->pivot->relationship ?? 'mother'); @endphp
                                <option value="mother" {{ $rel === 'mother' ? 'selected' : '' }}>Anne</option>
                                <option value="father" {{ $rel === 'father' ? 'selected' : '' }}>Baba</option>
                                <option value="guardian" {{ $rel === 'guardian' ? 'selected' : '' }}>Vasi</option>
                                <option value="other" {{ $rel === 'other' ? 'selected' : '' }}>Diğer</option>
                            </select>
                            <input type="hidden" name="student_ids[]" value="{{ $student->id }}">
                            <button type="button" class="remove-student text-red-600 hover:text-red-800 text-sm">Kaldır</button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @error('student_ids')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                           {{ old('is_active', $parent->is_active) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.parents.show', $parent) }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                İptal
            </a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                Güncelle
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function() {
    const findByTcUrl = '{{ route("admin.students.find-by-tc") }}';
    const listEl = document.getElementById('parent_students_list');
    const inputEl = document.getElementById('student_tc_input');
    const addBtn = document.getElementById('add_student_by_tc_btn');
    const messageEl = document.getElementById('student_tc_message');
    const noHint = document.getElementById('no_students_hint');
    const addedIds = new Set();
    listEl.querySelectorAll('[data-student-id]').forEach(function(row) {
        addedIds.add(parseInt(row.getAttribute('data-student-id'), 10));
    });

    function showMessage(msg, isError) {
        messageEl.textContent = msg || '';
        messageEl.classList.toggle('hidden', !msg);
        messageEl.classList.toggle('text-red-600', isError !== false);
        messageEl.classList.toggle('text-green-600', msg && !isError);
    }

    function addRow(studentId, studentName) {
        if (addedIds.has(studentId)) {
            showMessage('Bu öğrenci zaten eklendi.', true);
            return;
        }
        addedIds.add(studentId);
        noHint.classList.add('hidden');
        const row = document.createElement('div');
        row.className = 'flex items-center justify-between py-2 border-b border-gray-100 last:border-0';
        row.dataset.studentId = studentId;
        row.innerHTML = '<span class="text-sm font-medium text-gray-900">' + (studentName || '') + '</span>' +
            '<div class="flex items-center gap-2">' +
            '<select name="relationships[' + studentId + ']" class="text-sm border rounded px-2 py-1">' +
            '<option value="mother">Anne</option><option value="father">Baba</option><option value="guardian">Vasi</option><option value="other">Diğer</option>' +
            '</select>' +
            '<input type="hidden" name="student_ids[]" value="' + studentId + '">' +
            '<button type="button" class="remove-student text-red-600 hover:text-red-800 text-sm">Kaldır</button>' +
            '</div>';
        listEl.appendChild(row);
        row.querySelector('.remove-student').addEventListener('click', function() {
            addedIds.delete(studentId);
            row.remove();
            if (listEl.querySelectorAll('[data-student-id]').length === 0) noHint.classList.remove('hidden');
        });
    }

    addBtn.addEventListener('click', function() {
        const tc = (inputEl.value || '').replace(/\D/g, '');
        if (tc.length !== 11) {
            showMessage('11 haneli TC Kimlik No girin.', true);
            return;
        }
        showMessage('');
        fetch(findByTcUrl + '?tc=' + encodeURIComponent(tc), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, status: r.status, data: d }; }).catch(function() { return { ok: false, data: null }; }); })
            .then(function(res) {
                if (res.ok && res.data && res.data.found) {
                    addRow(res.data.id, res.data.name);
                    inputEl.value = '';
                    showMessage('Öğrenci eklendi.', false);
                    setTimeout(function() { showMessage(''); }, 2000);
                } else {
                    showMessage((res.data && res.data.message) || 'Öğrenci bulunamadı.', true);
                }
            })
            .catch(function() {
                showMessage('İstek başarısız.', true);
            });
    });

    listEl.querySelectorAll('.remove-student').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const row = btn.closest('[data-student-id]');
            if (row) {
                addedIds.delete(parseInt(row.getAttribute('data-student-id'), 10));
                row.remove();
                if (listEl.querySelectorAll('[data-student-id]').length === 0) noHint.classList.remove('hidden');
            }
        });
    });
})();
</script>
@endpush
@endsection
