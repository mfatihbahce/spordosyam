<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Coach;
use App\Models\MakeupSession;
use App\Models\StudentMakeupClass;
use App\Services\ClassScheduleService;
use App\Services\SmsNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MakeupSessionController extends Controller
{
    public function __construct(
        protected ClassScheduleService $scheduleService
    ) {}

    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $school = Auth::user()->school;

        if (!$school || !$school->makeup_class_enabled) {
            abort(403, 'Telafi dersi özelliği aktif değil.');
        }

        $sessions = MakeupSession::where('school_id', $schoolId)
            ->with(['coach.user', 'branch'])
            ->withCount('studentMakeupClasses')
            ->orderBy('scheduled_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(15);

        return view('admin.makeup-sessions.index', compact('sessions'));
    }

    public function create()
    {
        $schoolId = Auth::user()->school_id;
        $school = Auth::user()->school;

        if (!$school || !$school->makeup_class_enabled) {
            abort(403, 'Telafi dersi özelliği aktif değil.');
        }

        $branches = Branch::where('school_id', $schoolId)->where('is_active', true)->get();
        $coaches = Coach::where('school_id', $schoolId)->where('is_active', true)->with('user')->get();

        return view('admin.makeup-sessions.create', compact('branches', 'coaches'));
    }

    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $school = Auth::user()->school;

        if (!$school || !$school->makeup_class_enabled) {
            abort(403, 'Telafi dersi özelliği aktif değil.');
        }

        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'coach_id' => 'required|exists:coaches,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'name' => 'nullable|string|max:255',
        ], [
            'end_time.after' => 'Bitiş saati, başlangıç saatinden sonra olmalıdır. Örneğin başlangıç 23:00 ise bitiş 10:00 olamaz.',
        ]);

        if ($validated['scheduled_date'] === now()->toDateString() && $validated['start_time'] < now()->format('H:i')) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['start_time' => 'Bugün için geçmiş saat seçilemez.']);
        }

        $coach = Coach::where('id', $validated['coach_id'])->where('school_id', $schoolId)->firstOrFail();
        if ($validated['branch_id'] ?? null) {
            Branch::where('id', $validated['branch_id'])->where('school_id', $schoolId)->firstOrFail();
        }

        if ($this->scheduleService->hasScheduleConflict(
            $schoolId,
            $validated['scheduled_date'],
            $validated['start_time'],
            $validated['end_time'],
            null
        )) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['scheduled_date' => 'Bu tarih ve saatte başka bir ders veya telafi oturumu var. Çakışma olmaması için farklı bir slot seçin.']);
        }

        $validated['school_id'] = $schoolId;
        if (empty($validated['name'])) {
            $validated['name'] = 'Telafi Dersi - ' . \Carbon\Carbon::parse($validated['scheduled_date'])->format('d.m.Y') . ' ' . $validated['start_time'];
        }

        MakeupSession::create($validated);

        return redirect()->route('admin.makeup-sessions.index')
            ->with('success', 'Telafi dersi oluşturuldu. Öğrenci eklemek için detay sayfasına gidin.');
    }

    public function show(MakeupSession $makeupSession)
    {
        $schoolId = Auth::user()->school_id;
        if ($makeupSession->school_id !== $schoolId) {
            abort(403);
        }

        $makeupSession->load(['coach.user', 'branch', 'studentMakeupClasses.student', 'studentMakeupClasses.makeupClass.originalClass']);

        return view('admin.makeup-sessions.show', compact('makeupSession'));
    }

    public function edit(MakeupSession $makeupSession)
    {
        $schoolId = Auth::user()->school_id;
        if ($makeupSession->school_id !== $schoolId) {
            abort(403);
        }

        $branches = Branch::where('school_id', $schoolId)->where('is_active', true)->get();
        $coaches = Coach::where('school_id', $schoolId)->where('is_active', true)->with('user')->get();

        return view('admin.makeup-sessions.edit', compact('makeupSession', 'branches', 'coaches'));
    }

    public function update(Request $request, MakeupSession $makeupSession)
    {
        $schoolId = Auth::user()->school_id;
        if ($makeupSession->school_id !== $schoolId) {
            abort(403);
        }

        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'coach_id' => 'required|exists:coaches,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'name' => 'nullable|string|max:255',
        ], [
            'end_time.after' => 'Bitiş saati, başlangıç saatinden sonra olmalıdır. Örneğin başlangıç 23:00 ise bitiş 10:00 olamaz.',
        ]);

        Coach::where('id', $validated['coach_id'])->where('school_id', $schoolId)->firstOrFail();
        if ($validated['branch_id'] ?? null) {
            Branch::where('id', $validated['branch_id'])->where('school_id', $schoolId)->firstOrFail();
        }

        if ($this->scheduleService->hasScheduleConflict(
            $schoolId,
            $validated['scheduled_date'],
            $validated['start_time'],
            $validated['end_time'],
            $makeupSession->id
        )) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['scheduled_date' => 'Bu tarih ve saatte başka bir ders veya telafi oturumu var.']);
        }

        $makeupSession->update($validated);

        return redirect()->route('admin.makeup-sessions.show', $makeupSession)
            ->with('success', 'Telafi dersi güncellendi.');
    }

    public function destroy(MakeupSession $makeupSession)
    {
        $schoolId = Auth::user()->school_id;
        if ($makeupSession->school_id !== $schoolId) {
            abort(403);
        }

        $makeupSession->studentMakeupClasses()->update(['makeup_session_id' => null, 'status' => 'pending']);
        $makeupSession->delete();

        return redirect()->route('admin.makeup-sessions.index')
            ->with('success', 'Telafi dersi silindi. Öğrenciler tekrar bekleyen listesine alındı.');
    }

    /**
     * Bu telafi dersine öğrenci ekle (telafi bekleyen listeden)
     */
    public function addStudents(Request $request, MakeupSession $makeupSession)
    {
        $schoolId = Auth::user()->school_id;
        if ($makeupSession->school_id !== $schoolId) {
            abort(403);
        }

        $validated = $request->validate([
            'student_makeup_ids' => 'required|array',
            'student_makeup_ids.*' => 'integer|exists:student_makeup_classes,id',
        ]);

        $updated = 0;
        foreach ($validated['student_makeup_ids'] as $id) {
            $sm = StudentMakeupClass::where('id', $id)
                ->where('status', 'pending')
                ->whereHas('student', fn ($q) => $q->where('school_id', $schoolId))
                ->first();
            if ($sm && !$sm->makeup_session_id) {
                $sm->update([
                    'makeup_session_id' => $makeupSession->id,
                    'scheduled_date' => $makeupSession->scheduled_date,
                    'status' => 'scheduled',
                ]);
                $sm->makeupClass?->update([
                    'scheduled_date' => $makeupSession->scheduled_date,
                    'status' => 'scheduled',
                ]);
                $updated++;
            }
        }

        // Telafi atandı SMS: veli + antrenör (superadmin ayarına göre)
        if ($updated > 0) {
            $makeupSession->load(['coach', 'branch']);
            $dateStr = $makeupSession->scheduled_date ? \Carbon\Carbon::parse($makeupSession->scheduled_date)->format('d.m.Y') : '';
            $timeStr = $makeupSession->start_time ?? '';
            $branchName = $makeupSession->branch->name ?? '';
            $smsService = app(SmsNotificationService::class);
            foreach ($validated['student_makeup_ids'] as $id) {
                $sm = StudentMakeupClass::where('id', $id)->where('makeup_session_id', $makeupSession->id)
                    ->with('student.parents')->first();
                if ($sm && $sm->student) {
                    $parent = $sm->student->parents->first();
                    if ($parent && !empty($parent->phone)) {
                        $msg = "{$sm->student->first_name} {$sm->student->last_name} icin telafi: {$dateStr}, {$timeStr}" . ($branchName ? ", {$branchName}" : '') . ". Spordosyam";
                        $smsService->sendIfEnabled('makeup_assigned', $parent->phone, $msg, $parent->user);
                    }
                }
            }
            if ($makeupSession->coach && !empty($makeupSession->coach->phone)) {
                $msg = "Size atanan telafi dersi: {$dateStr}, {$timeStr}. Spordosyam";
                $smsService->sendIfEnabled('coach_makeup_assigned', $makeupSession->coach->phone, $msg);
            }
        }

        return redirect()->route('admin.makeup-sessions.show', $makeupSession)
            ->with('success', $updated . ' öğrenci telafi dersine eklendi.');
    }

    /**
     * Telafi bekleyen öğrencileri listele (AJAX - derse ekleme modalı için)
     */
    public function pendingStudents(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $school = Auth::user()->school;
        if (!$school || !$school->makeup_class_enabled) {
            return response()->json(['students' => []], 403);
        }

        $list = StudentMakeupClass::where('status', 'pending')
            ->whereNull('makeup_session_id')
            ->whereHas('student', fn ($q) => $q->where('school_id', $schoolId))
            ->with(['student', 'makeupClass.originalClass'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($sm) => [
                'id' => $sm->id,
                'student_name' => $sm->student->first_name . ' ' . $sm->student->last_name,
                'original_class' => $sm->makeupClass?->originalClass?->name ?? '-',
            ]);

        return response()->json(['students' => $list]);
    }
}
