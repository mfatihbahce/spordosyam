@extends('layouts.panel')

@section('title', 'Yeni Sınıf Ekle')
@section('page-title', 'Yeni Sınıf Ekle')
@section('page-description', 'Yeni bir sınıf oluşturun ve öğrencileri atayın')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-5 py-3 border-b border-gray-200">
        <h3 class="text-base font-semibold text-gray-900 flex items-center">
            <i class="fas fa-chalkboard-teacher text-indigo-600 mr-2 text-sm"></i>
            Sınıf Bilgileri
        </h3>
    </div>

    <form action="{{ route('admin.classes.store') }}" method="POST" id="classForm" class="p-5">
        @csrf
        
        <!-- Temel Bilgiler -->
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="name" class="block text-xs font-medium text-gray-700 mb-1.5">
                        <i class="fas fa-tag text-indigo-600 mr-1 text-xs"></i>
                        Sınıf Adı <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required
                           class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('name') border-red-500 @enderror"
                           placeholder="Örn: Futbol A Takımı" value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1 text-xs"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="sport_branch_id" class="block text-xs font-medium text-gray-700 mb-1.5">
                        <i class="fas fa-futbol text-indigo-600 mr-1 text-xs"></i>
                        Branş <span class="text-red-500">*</span>
                    </label>
                    <select name="sport_branch_id" id="sport_branch_id" required
                            class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('sport_branch_id') border-red-500 @enderror">
                        <option value="">Branş Seçiniz</option>
                        @foreach($sportBranches as $sportBranch)
                            <option value="{{ $sportBranch->id }}" {{ old('sport_branch_id') == $sportBranch->id ? 'selected' : '' }}>
                                {{ $sportBranch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('sport_branch_id')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1 text-xs"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="branch_id" class="block text-xs font-medium text-gray-700 mb-1.5">
                        <i class="fas fa-building text-indigo-600 mr-1 text-xs"></i>
                        Şube <span class="text-gray-500 text-xs">(Opsiyonel)</span>
                    </label>
                    <select name="branch_id" id="branch_id"
                            class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('branch_id') border-red-500 @enderror">
                        <option value="">Şube Seçiniz</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1 text-xs"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="coach_id" class="block text-xs font-medium text-gray-700 mb-1.5">
                        <i class="fas fa-user-tie text-indigo-600 mr-1 text-xs"></i>
                        Antrenör <span class="text-gray-500 text-xs">(Opsiyonel)</span>
                    </label>
                    <select name="coach_id" id="coach_id"
                            class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('coach_id') border-red-500 @enderror">
                        <option value="">Antrenör Seçiniz</option>
                        @foreach($coaches as $coach)
                            <option value="{{ $coach->id }}" {{ old('coach_id') == $coach->id ? 'selected' : '' }}>
                                {{ $coach->user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('coach_id')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1 text-xs"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="capacity" class="block text-xs font-medium text-gray-700 mb-1.5">
                        <i class="fas fa-users text-indigo-600 mr-1 text-xs"></i>
                        Kontenjan
                    </label>
                    <div class="relative">
                        <input type="number" name="capacity" id="capacity" min="1"
                               class="block w-full px-3 py-2 pr-12 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('capacity') border-red-500 @enderror"
                               value="{{ old('capacity', 20) }}" placeholder="20">
                        <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-xs">kişi</span>
                        </div>
                    </div>
                    @error('capacity')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1 text-xs"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Ders Günleri ve Saatleri -->
            <div class="border-t border-gray-200 pt-4">
                <div class="mb-3">
                    <h4 class="text-sm font-semibold text-gray-900 flex items-center mb-1">
                        <i class="fas fa-calendar-alt text-indigo-600 mr-2 text-sm"></i>
                        Ders Günleri ve Saatleri
                    </h4>
                    <p class="text-xs text-gray-500">Her gün için farklı saat belirleyebilirsiniz</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    @php
                        $days = [
                            'monday' => ['label' => 'Pazartesi', 'icon' => 'fa-calendar-day'],
                            'tuesday' => ['label' => 'Salı', 'icon' => 'fa-calendar-day'],
                            'wednesday' => ['label' => 'Çarşamba', 'icon' => 'fa-calendar-day'],
                            'thursday' => ['label' => 'Perşembe', 'icon' => 'fa-calendar-day'],
                            'friday' => ['label' => 'Cuma', 'icon' => 'fa-calendar-day'],
                            'saturday' => ['label' => 'Cumartesi', 'icon' => 'fa-calendar-day'],
                            'sunday' => ['label' => 'Pazar', 'icon' => 'fa-calendar-day']
                        ];
                        $oldDays = old('class_days', []);
                        $oldSchedule = old('class_schedule', []);
                    @endphp
                    @foreach($days as $key => $day)
                        <div class="day-schedule-item flex items-center gap-2 p-2.5 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50/50 transition-all">
                            <label class="flex items-center flex-shrink-0 cursor-pointer group">
                                <input type="checkbox" name="class_days[]" value="{{ $key }}" 
                                       class="day-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                       {{ in_array($key, $oldDays) ? 'checked' : '' }}>
                                <span class="ml-2 text-xs font-medium text-gray-700 group-hover:text-indigo-600 transition-colors min-w-[75px]">
                                    <i class="fas {{ $day['icon'] }} text-indigo-500 mr-1 text-xs"></i>
                                    {{ $day['label'] }}
                                </span>
                            </label>
                            <div class="flex-1 day-time-input" style="display: {{ in_array($key, $oldDays) ? 'block' : 'none' }};">
                                <div class="flex items-center gap-1.5">
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-500 mb-0.5 block">Başlangıç</label>
                                        <input type="time" 
                                               name="class_schedule[{{ $key }}][start_time]" 
                                               value="{{ $oldSchedule[$key]['start_time'] ?? '' }}"
                                               class="day-time-input-start w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                                               {{ in_array($key, $oldDays) ? '' : 'disabled' }}>
                                    </div>
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-500 mb-0.5 block">Bitiş</label>
                                        <input type="time" 
                                               name="class_schedule[{{ $key }}][end_time]" 
                                               value="{{ $oldSchedule[$key]['end_time'] ?? '' }}"
                                               class="day-time-input-end w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                                               {{ in_array($key, $oldDays) ? '' : 'disabled' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('class_days')
                    <p class="mt-2 text-xs text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1 text-xs"></i>
                        {{ $message }}
                    </p>
                @enderror
                @error('class_schedule')
                    <p class="mt-2 text-xs text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1 text-xs"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Açıklama -->
            <div class="border-t border-gray-200 pt-4">
                <label for="description" class="block text-xs font-medium text-gray-700 mb-1.5">
                    <i class="fas fa-align-left text-indigo-600 mr-1 text-xs"></i>
                    Açıklama <span class="text-gray-500 text-xs">(Opsiyonel)</span>
                </label>
                <textarea name="description" id="description" rows="3"
                          class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all resize-none @error('description') border-red-500 @enderror"
                          placeholder="Sınıf hakkında ek bilgiler, notlar veya açıklamalar...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1 text-xs"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Ders Bitiş Tarihi -->
            <div class="border-t border-gray-200 pt-4">
                <label for="end_date" class="block text-xs font-medium text-gray-700 mb-1.5">
                    <i class="fas fa-calendar-times text-indigo-600 mr-1 text-xs"></i>
                    Ders Bitiş Tarihi <span class="text-gray-500 text-xs">(Opsiyonel)</span>
                </label>
                <input type="date" 
                       name="end_date" 
                       id="end_date"
                       value="{{ old('end_date') }}"
                       min="{{ now()->format('Y-m-d') }}"
                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('end_date') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Belirtilmezse sınıf süresiz devam eder. Bitiş tarihi geçerse sınıf otomatik olarak kapanır.
                </p>
                @error('end_date')
                    <p class="mt-1 text-xs text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1 text-xs"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="border-t border-gray-200 pt-4">
                <label for="default_credits" class="block text-xs font-medium text-gray-700 mb-1.5">
                    <i class="fas fa-graduation-cap text-indigo-600 mr-1 text-xs"></i>
                    Varsayılan Ders Hakkı
                </label>
                <input type="number" name="default_credits" id="default_credits" min="1"
                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('default_credits') border-red-500 @enderror"
                       value="{{ old('default_credits', 8) }}" placeholder="8">
                <p class="mt-1 text-xs text-gray-500">Bu sınıfa kayıt olan her öğrenci için tanımlanacak ders hakkı sayısı</p>
                @error('default_credits')
                    <p class="mt-1 text-xs text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1 text-xs"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-5 pt-4 border-t border-gray-200 flex justify-end space-x-2">
            <a href="{{ route('admin.classes.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all">
                <i class="fas fa-times mr-1.5"></i>
                İptal
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-sm hover:shadow">
                <i class="fas fa-save mr-1.5"></i>
                Kaydet
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tüm gün checkbox'larını dinle
    document.querySelectorAll('.day-checkbox').forEach(function(checkbox) {
        // İlk yüklemede mevcut durumu kontrol et
        const dayItem = checkbox.closest('.day-schedule-item');
        const timeInput = dayItem.querySelector('.day-time-input');
        
        if (checkbox.checked) {
            timeInput.style.display = 'block';
            dayItem.classList.add('border-indigo-300', 'bg-indigo-50/50');
        }
        
        checkbox.addEventListener('change', function() {
            const startInput = timeInput.querySelector('.day-time-input-start');
            const endInput = timeInput.querySelector('.day-time-input-end');
            if (this.checked) {
                timeInput.style.display = 'block';
                dayItem.classList.add('border-indigo-300', 'bg-indigo-50/50');
                if (startInput) startInput.removeAttribute('disabled');
                if (endInput) endInput.removeAttribute('disabled');
            } else {
                timeInput.style.display = 'none';
                dayItem.classList.remove('border-indigo-300', 'bg-indigo-50/50');
                if (startInput) { startInput.value = ''; startInput.setAttribute('disabled', 'disabled'); }
                if (endInput) { endInput.value = ''; endInput.setAttribute('disabled', 'disabled'); }
            }
        });
    });
});
</script>
@endpush
@endsection
