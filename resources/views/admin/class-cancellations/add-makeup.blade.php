@extends('layouts.panel')

@section('title', 'Telafi Ekle - ' . ($classCancellation->classModel->name ?? ''))
@section('page-title', 'Telafi Ekle')
@section('page-description', $classCancellation->classModel->name ?? 'İptal/Erteleme için telafi dersi oluştur')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.class-cancellations.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
        <i class="fas fa-arrow-left mr-2 text-indigo-500"></i>
        İptal Olan Derslere Dön
    </a>
</div>

@if(session('info'))
    <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-100 text-blue-800 text-sm flex items-start gap-3">
        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
        <span>{{ session('info') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-800 text-sm flex items-start gap-3">
        <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
    {{-- Sol: Form --}}
    <div class="lg:col-span-8">
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 overflow-hidden border border-gray-100 ring-1 ring-gray-200/50">
            <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-600 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                        <i class="fas fa-calendar-plus text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Telafi Ekle</h2>
                        <p class="text-indigo-100 text-sm mt-0.5">{{ $classCancellation->classModel->name }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                <div id="validationAlert" class="hidden mb-6 rounded-xl border-2 border-red-200 bg-red-50/90 px-4 py-3 text-red-800 text-sm font-medium flex items-start gap-3 ring-2 ring-red-100/50">
                    <i class="fas fa-exclamation-triangle text-red-500 mt-0.5 flex-shrink-0"></i>
                    <span id="validationAlertText"></span>
                </div>

                <form id="telafiEkleForm" method="post" action="{{ route('admin.class-cancellations.store-makeup', $classCancellation) }}">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <label for="telafiScheduledDate" class="block text-sm font-semibold text-gray-700 mb-2">Yeni Tarih <span class="text-red-500">*</span></label>
                            <input type="date" name="scheduled_date" id="telafiScheduledDate" required
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow"
                                   min="{{ date('Y-m-d') }}" value="{{ old('scheduled_date') }}">
                            <p class="mt-1.5 text-xs text-gray-500">Bugün veya ileri bir tarih seçin.</p>
                        </div>
                        <div>
                            <label for="telafiStartTime" class="block text-sm font-semibold text-gray-700 mb-2">Başlangıç saati <span class="text-red-500">*</span></label>
                            <input type="time" name="start_time" id="telafiStartTime" required
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow"
                                   value="{{ old('start_time') }}">
                        </div>
                        <div>
                            <label for="telafiEndTime" class="block text-sm font-semibold text-gray-700 mb-2">Bitiş saati <span class="text-red-500">*</span></label>
                            <input type="time" name="end_time" id="telafiEndTime" required
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow"
                                   value="{{ old('end_time') }}">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="telafiCoachId" class="block text-sm font-semibold text-gray-700 mb-2">Antrenör <span class="text-red-500">*</span></label>
                            <select name="coach_id" id="telafiCoachId" required
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow bg-white">
                                <option value="">Antrenör seçin</option>
                                @foreach($coaches as $coach)
                                    <option value="{{ $coach->id }}" {{ old('coach_id') == $coach->id ? 'selected' : '' }}>
                                        {{ $coach->user->name ?? $coach->id }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1.5 text-xs text-gray-500">Seçilen antrenörün bu saatte başka dersi olmamalı.</p>
                        </div>
                    </div>
                    <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <a href="{{ route('admin.class-cancellations.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                            İptal
                        </a>
                        <button type="submit" id="telafiSubmitBtn" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all shadow-sm">
                            <i class="fas fa-check mr-2"></i>
                            Telafi Dersi Oluştur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Sağ: Özet ve bilgi --}}
    <div class="lg:col-span-4 space-y-6">
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 overflow-hidden border border-gray-100 ring-1 ring-gray-200/50 p-6">
            <p class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <i class="fas fa-users text-indigo-500"></i>
                Öğrenci bilgisi
            </p>
            <div class="space-y-3">
                <div class="flex items-center justify-between rounded-xl bg-gray-50 px-4 py-3 border border-gray-100">
                    <span class="text-sm text-gray-600">Toplam öğrenci</span>
                    <span class="font-bold text-gray-900 text-lg">{{ $totalStudents }}</span>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-indigo-50 px-4 py-3 border border-indigo-100">
                    <span class="text-sm text-indigo-700">Bu derse eklenecek</span>
                    <span class="font-bold text-indigo-600 text-lg">{{ $toBeAddedCount }}</span>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-gray-50 to-indigo-50/50 rounded-2xl border border-gray-100 ring-1 ring-gray-200/30 p-6">
            <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                <i class="fas fa-info-circle text-indigo-500"></i>
                İpuçları
            </p>
            <ul class="text-sm text-gray-600 space-y-2">
                <li class="flex items-start gap-2">
                    <i class="fas fa-check text-indigo-500 mt-0.5 text-xs"></i>
                    Bitiş saati başlangıç saatinden sonra olmalıdır.
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check text-indigo-500 mt-0.5 text-xs"></i>
                    Geçmiş tarih ve saat seçilemez.
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check text-indigo-500 mt-0.5 text-xs"></i>
                    Antrenörün aynı saatte başka dersi varsa telafi oluşturulamaz.
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check text-indigo-500 mt-0.5 text-xs"></i>
                    Aynı saatte başka ders varsa onay istenecektir.
                </li>
            </ul>
        </div>
    </div>
</div>

{{-- Uyarı modalı (alert yerine) --}}
<div id="alertModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" id="alertModalBackdrop"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl ring-1 ring-gray-200/80 max-w-md w-full p-6" role="dialog" aria-labelledby="alertModalTitle" aria-modal="true">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center ring-4 ring-red-100/50">
                    <i class="fas fa-exclamation-circle text-red-500 text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <h3 id="alertModalTitle" class="text-xl font-bold text-gray-900 mb-2">Uyarı</h3>
                    <p id="alertModalMessage" class="text-sm text-gray-600 leading-relaxed"></p>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" id="alertModalOk" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all shadow-sm hover:shadow">
                    Tamam
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Onay modalı (confirm yerine) --}}
<div id="confirmModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" id="confirmModalBackdrop"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl ring-1 ring-gray-200/80 max-w-md w-full p-6" role="dialog" aria-labelledby="confirmModalTitle" aria-modal="true">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100 flex items-center justify-center ring-4 ring-amber-100/50">
                    <i class="fas fa-question-circle text-amber-600 text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <h3 id="confirmModalTitle" class="text-xl font-bold text-gray-900 mb-2">Onay</h3>
                    <p id="confirmModalMessage" class="text-sm text-gray-600 leading-relaxed"></p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" id="confirmModalCancel" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
                    İptal
                </button>
                <button type="button" id="confirmModalOk" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all shadow-sm hover:shadow">
                    Evet, oluştur
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const conflictCheckUrl = '{{ route("admin.class-cancellations.check-conflict") }}';
    const storeMakeupUrl = '{{ route("admin.class-cancellations.store-makeup", $classCancellation) }}';
    let conflictCheckTimeout = null;

    function showAlertModal(message) {
        const modal = document.getElementById('alertModal');
        const msgEl = document.getElementById('alertModalMessage');
        const okBtn = document.getElementById('alertModalOk');
        const backdrop = document.getElementById('alertModalBackdrop');
        if (!modal || !msgEl) return;
        msgEl.textContent = message || '';
        modal.classList.remove('hidden');
        function close() {
            modal.classList.add('hidden');
        }
        okBtn.addEventListener('click', close, { once: true });
        backdrop.addEventListener('click', close, { once: true });
    }

    function showConfirmModal(message) {
        return new Promise(function(resolve) {
            const modal = document.getElementById('confirmModal');
            const msgEl = document.getElementById('confirmModalMessage');
            const okBtn = document.getElementById('confirmModalOk');
            const cancelBtn = document.getElementById('confirmModalCancel');
            const backdrop = document.getElementById('confirmModalBackdrop');
            if (!modal || !msgEl) { resolve(false); return; }
            msgEl.textContent = message || '';
            modal.classList.remove('hidden');
            function close(result) {
                modal.classList.add('hidden');
                okBtn.removeEventListener('click', onOk);
                cancelBtn.removeEventListener('click', onCancel);
                backdrop.removeEventListener('click', onCancel);
                resolve(result);
            }
            function onOk() { close(true); }
            function onCancel() { close(false); }
            okBtn.addEventListener('click', onOk);
            cancelBtn.addEventListener('click', onCancel);
            backdrop.addEventListener('click', onCancel);
        });
    }

    function getTodayMinTime() {
        const d = new Date();
        const h = d.getHours(), m = d.getMinutes();
        return (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m;
    }
    function getTodayDateStr() {
        const d = new Date();
        const y = d.getFullYear(), m = d.getMonth() + 1, day = d.getDate();
        return y + '-' + (m < 10 ? '0' : '') + m + '-' + (day < 10 ? '0' : '') + day;
    }

    function applyMinTime() {
        const dateEl = document.getElementById('telafiScheduledDate');
        const startEl = document.getElementById('telafiStartTime');
        const endEl = document.getElementById('telafiEndTime');
        if (!dateEl || !startEl) return;
        if (dateEl.value === getTodayDateStr()) {
            startEl.min = getTodayMinTime();
        } else {
            startEl.removeAttribute('min');
        }
        if (endEl && startEl.value) {
            endEl.min = startEl.value;
        } else if (endEl) {
            endEl.removeAttribute('min');
        }
    }

    function getValidationError() {
        const date = document.getElementById('telafiScheduledDate').value;
        const startTime = document.getElementById('telafiStartTime').value;
        const endTime = document.getElementById('telafiEndTime').value;
        if (date && date === getTodayDateStr() && startTime && startTime < getTodayMinTime()) {
            return 'Geçmiş saat seçilemez. Başlangıç saati şu anki saatten ileri olmalıdır.';
        }
        if (startTime && endTime && endTime <= startTime) {
            return 'Bitiş saati başlangıç saatinden sonra olmalıdır.';
        }
        return null;
    }

    function setValidationAlert(msg) {
        const alertEl = document.getElementById('validationAlert');
        const textEl = document.getElementById('validationAlertText');
        if (textEl) textEl.textContent = msg || '';
        if (msg) alertEl.classList.remove('hidden'); else alertEl.classList.add('hidden');
    }

    function checkConflictLive() {
        const validationErr = getValidationError();
        if (validationErr) {
            setValidationAlert(validationErr);
            return;
        }
        const date = document.getElementById('telafiScheduledDate').value;
        const startTime = document.getElementById('telafiStartTime').value;
        const endTime = document.getElementById('telafiEndTime').value;
        const coachId = document.getElementById('telafiCoachId').value;
        if (!date || !startTime || !endTime) {
            setValidationAlert('');
            return;
        }
        if (conflictCheckTimeout) clearTimeout(conflictCheckTimeout);
        conflictCheckTimeout = setTimeout(function() {
            const params = new URLSearchParams({ date: date, start_time: startTime, end_time: endTime });
            if (coachId) params.set('coach_id', coachId);
            fetch(conflictCheckUrl + '?' + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.conflict && data.message) {
                    setValidationAlert(data.message);
                } else {
                    setValidationAlert('');
                }
            })
            .catch(function() {
                setValidationAlert('');
            });
        }, 400);
    }

    document.getElementById('telafiScheduledDate').addEventListener('change', function() {
        applyMinTime();
        checkConflictLive();
    });
    document.getElementById('telafiStartTime').addEventListener('change', function() {
        applyMinTime();
        checkConflictLive();
    });
    document.getElementById('telafiEndTime').addEventListener('change', function() {
        applyMinTime();
        checkConflictLive();
    });
    document.getElementById('telafiCoachId').addEventListener('change', checkConflictLive);

    document.getElementById('telafiEkleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const date = document.getElementById('telafiScheduledDate').value;
        const startTime = document.getElementById('telafiStartTime').value;
        const endTime = document.getElementById('telafiEndTime').value;
        const coachId = document.getElementById('telafiCoachId').value;
        if (!date || !startTime || !endTime) {
            setValidationAlert('Lütfen tarih, başlangıç saati ve bitiş saati alanlarını doldurun.');
            return;
        }
        if (!coachId) {
            setValidationAlert('Lütfen bir antrenör seçin.');
            return;
        }
        const validationErr = getValidationError();
        if (validationErr) {
            setValidationAlert(validationErr);
            return;
        }
        setValidationAlert('');

        const form = this;
        const btn = document.getElementById('telafiSubmitBtn');
        function doSubmit(forceCreate) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Kaydediliyor...';
            const formData = new FormData(form);
            if (forceCreate) formData.set('force_create', '1');
            fetch(storeMakeupUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, status: r.status, data: data }; }).catch(function() { return { ok: r.ok, status: r.status, data: null }; }); })
            .then(function(res) {
                if (res.ok && res.data && res.data.success) {
                    if (res.data.redirect) window.location.href = res.data.redirect;
                    else window.location.reload();
                    return;
                }
                if (res.status === 422 && res.data && res.data.require_confirm) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check mr-2"></i> Telafi Dersi Oluştur';
                    var confirmMsg = (res.data && res.data.message) || 'Bu tarih ve saatte çakışma var. Yine de oluşturmak istiyor musunuz?';
                    showConfirmModal(confirmMsg).then(function(ok) {
                        if (ok) doSubmit(true);
                    });
                    return;
                }
                var msg = (res.data && res.data.message) || (res.data && res.data.errors && Object.values(res.data.errors).flat().join(' ')) || 'Bir hata oluştu.';
                showAlertModal(msg);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check mr-2"></i> Telafi Dersi Oluştur';
            })
            .catch(function() {
                showAlertModal('Bir hata oluştu. Lütfen tekrar deneyin.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check mr-2"></i> Telafi Dersi Oluştur';
            });
        }
        doSubmit(false);
    });

    applyMinTime();
})();
</script>
@endpush
@endsection
