@extends('layouts.panel')

@section('title', 'Yeni Gelişim Notu')
@section('page-title', 'Yeni Gelişim Notu Ekle')

@section('sidebar-menu')
    @include('coach.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('coach.student-progress.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sınıf</label>
                    <select name="class_id" id="class_id" onchange="loadStudents()" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Seçiniz</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Öğrenci</label>
                    <select name="student_id" id="student_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Önce sınıf seçiniz</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tarih</label>
                    <input type="date" name="progress_date" value="{{ date('Y-m-d') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notlar</label>
                    <textarea name="notes" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('coach.student-progress.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    İptal
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const classes = @json($classes->keyBy('id'));

function loadStudents() {
    const classId = document.getElementById('class_id').value;
    const studentSelect = document.getElementById('student_id');
    studentSelect.innerHTML = '<option value="">Seçiniz</option>';
    
    if (classId && classes[classId]) {
        classes[classId].students.forEach(student => {
            const option = document.createElement('option');
            option.value = student.id;
            option.textContent = student.first_name + ' ' + student.last_name;
            studentSelect.appendChild(option);
        });
    }
}
</script>
@endsection
