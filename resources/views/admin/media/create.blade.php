@extends('layouts.panel')

@section('title', 'Yeni Medya Yükle')
@section('page-title', 'Yeni Medya Yükle')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Başlık *</label>
                <input type="text" name="title" id="title" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border"
                       value="{{ old('title') }}">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="file" class="block text-sm font-medium text-gray-700">Dosya *</label>
                <input type="file" name="file" id="file" required accept="image/*,application/pdf,video/*"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border"
                       onchange="previewFile(this)">
                <p class="mt-1 text-xs text-gray-500">Desteklenen formatlar: JPG, PNG, PDF, MP4 (Max: 10MB)</p>
                @error('file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div id="filePreview" class="mt-2 hidden">
                    <img id="previewImage" src="" alt="Preview" class="max-w-full h-48 object-cover rounded">
                </div>
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700">Açıklama</label>
                <textarea name="description" id="description" rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="target_type" class="block text-sm font-medium text-gray-700 mb-2">Hedef Seçimi *</label>
                <select name="target_type" id="target_type" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border"
                        onchange="toggleTargets()">
                    <option value="all" {{ old('target_type') == 'all' ? 'selected' : '' }}>Tümü (Herkes görebilir)</option>
                    <option value="branch" {{ old('target_type') == 'branch' ? 'selected' : '' }}>Şube</option>
                    <option value="sport_branch" {{ old('target_type') == 'sport_branch' ? 'selected' : '' }}>Branş</option>
                    <option value="class" {{ old('target_type') == 'class' ? 'selected' : '' }}>Sınıf</option>
                    <option value="student" {{ old('target_type') == 'student' ? 'selected' : '' }}>Öğrenci</option>
                </select>
                @error('target_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="branchTargets" class="md:col-span-2 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Şubeler</label>
                <div class="border rounded-lg p-4 max-h-60 overflow-y-auto">
                    @forelse($branches as $branch)
                        <label class="flex items-center mb-2">
                            <input type="checkbox" name="target_ids[]" value="{{ $branch->id }}"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">{{ $branch->name }}</span>
                        </label>
                    @empty
                        <p class="text-sm text-gray-500">Henüz şube bulunmamaktadır.</p>
                    @endforelse
                </div>
            </div>

            <div id="sportBranchTargets" class="md:col-span-2 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Branşlar</label>
                <div class="border rounded-lg p-4 max-h-60 overflow-y-auto">
                    @forelse($sportBranches as $sportBranch)
                        <label class="flex items-center mb-2">
                            <input type="checkbox" name="target_ids[]" value="{{ $sportBranch->id }}"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">{{ $sportBranch->name }}</span>
                        </label>
                    @empty
                        <p class="text-sm text-gray-500">Henüz branş bulunmamaktadır.</p>
                    @endforelse
                </div>
            </div>

            <div id="classTargets" class="md:col-span-2 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Sınıflar</label>
                <div class="border rounded-lg p-4 max-h-60 overflow-y-auto">
                    @forelse($classes as $class)
                        <label class="flex items-center mb-2">
                            <input type="checkbox" name="target_ids[]" value="{{ $class->id }}"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">{{ $class->name }}</span>
                        </label>
                    @empty
                        <p class="text-sm text-gray-500">Henüz sınıf bulunmamaktadır.</p>
                    @endforelse
                </div>
            </div>

            <div id="studentTargets" class="md:col-span-2 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Öğrenciler</label>
                <div class="border rounded-lg p-4 max-h-60 overflow-y-auto">
                    @forelse($students as $student)
                        <label class="flex items-center mb-2">
                            <input type="checkbox" name="target_ids[]" value="{{ $student->id }}"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">{{ $student->first_name }} {{ $student->last_name }}</span>
                        </label>
                    @empty
                        <p class="text-sm text-gray-500">Henüz öğrenci bulunmamaktadır.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.media.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                İptal
            </a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                Yükle
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleTargets() {
    const targetType = document.getElementById('target_type').value;
    
    // Tüm hedef alanlarını gizle
    document.getElementById('branchTargets').classList.add('hidden');
    document.getElementById('sportBranchTargets').classList.add('hidden');
    document.getElementById('classTargets').classList.add('hidden');
    document.getElementById('studentTargets').classList.add('hidden');
    
    // Seçilen hedef tipine göre göster
    if (targetType === 'branch') {
        document.getElementById('branchTargets').classList.remove('hidden');
    } else if (targetType === 'sport_branch') {
        document.getElementById('sportBranchTargets').classList.remove('hidden');
    } else if (targetType === 'class') {
        document.getElementById('classTargets').classList.remove('hidden');
    } else if (targetType === 'student') {
        document.getElementById('studentTargets').classList.remove('hidden');
    }
}

function previewFile(input) {
    const file = input.files[0];
    const preview = document.getElementById('filePreview');
    const previewImage = document.getElementById('previewImage');
    
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
}

// Sayfa yüklendiğinde hedef seçimini kontrol et
document.addEventListener('DOMContentLoaded', function() {
    toggleTargets();
});
</script>
@endpush
@endsection
