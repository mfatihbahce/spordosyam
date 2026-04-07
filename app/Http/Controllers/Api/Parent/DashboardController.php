<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassCancellation;
use App\Models\Media;
use App\Models\ParentModel;
use App\Models\StudentMakeupClass;
use App\Models\Payment;
use App\Models\StudentFee;
use App\Models\StudentProgress;
use App\Services\ParentCalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Veli dashboard - platformdaki tüm verilerle birebir aynı.
     * Stats, yaklaşan ödemeler, son yoklamalar, son gelişim notları, son paylaşımlar, ders takvimi.
     *
     * Query: ?student_id=123 — Sadece bu öğrenciye ait veriler (mobil öğrenci seçimi için).
     * student_id yoksa tüm öğrenciler.
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        $students = $parent->students()->with('classModel')->get();
        $studentId = $request->query('student_id') ? (int) $request->query('student_id') : null;

        // Tek öğrenci seçiliyse ve veliye ait değilse hata
        if ($studentId !== null) {
            if (!$students->contains('id', $studentId)) {
                return response()->json(['message' => 'Bu öğrenci size ait değil.'], 403);
            }
            $studentIds = collect([$studentId]);
        } else {
            $studentIds = $students->pluck('id');
        }

        $totalAttendances = Attendance::whereIn('student_id', $studentIds)->count();
        $presentAttendances = Attendance::whereIn('student_id', $studentIds)->where('status', 'present')->count();
        $absentAttendances = Attendance::whereIn('student_id', $studentIds)->where('status', 'absent')->count();

        $stats = [
            'total_children' => $studentIds->count(),
            'attendance_rate' => $this->calculateAttendanceRate($studentIds),
            'total_attendances' => $totalAttendances,
            'present_attendances' => $presentAttendances,
            'absent_attendances' => $absentAttendances,
            'pending_fees' => (float) StudentFee::whereIn('student_id', $studentIds)->where('status', 'pending')->sum('amount'),
            'total_paid' => (float) Payment::whereIn('student_fee_id', StudentFee::whereIn('student_id', $studentIds)->pluck('id'))
                ->where('status', 'completed')->sum('amount'),
            'total_progress' => StudentProgress::whereIn('student_id', $studentIds)->count(),
        ];

        $minimal = filter_var($request->query('minimal'), FILTER_VALIDATE_BOOLEAN);
        $schoolId = $parent->school_id;
        $filteredStudents = $studentId !== null ? $students->where('id', $studentId) : $students;
        $classIds = $filteredStudents->pluck('class_id')->filter()->unique();

        $recentMedia = $minimal ? collect() : Media::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->where(function ($query) use ($studentIds, $classIds) {
                $query->whereDoesntHave('targets')
                    ->orWhereHas('targets', function ($q) use ($studentIds, $classIds) {
                        $q->where(function ($q2) use ($studentIds) {
                            $q2->where('target_type', 'student')->whereIn('target_id', $studentIds);
                        })->orWhere(function ($q2) use ($classIds) {
                            $q2->where('target_type', 'class')->whereIn('target_id', $classIds);
                        });
                    });
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentAttendances = Attendance::whereIn('student_id', $studentIds)
            ->orderBy('attendance_date', 'desc')
            ->limit(10)
            ->with(['student', 'classModel', 'coach.user'])
            ->get();

        $upcomingFees = StudentFee::whereIn('student_id', $studentIds)
            ->where('status', 'pending')
            ->where('due_date', '<=', now()->addDays(7))
            ->with(['student', 'feePlan'])
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        $recentProgress = $minimal ? collect() : StudentProgress::whereIn('student_id', $studentIds)
            ->with(['student', 'classModel', 'coach.user'])
            ->orderBy('progress_date', 'desc')
            ->limit(5)
            ->get();

        $calendarEvents = $minimal ? [] : app(ParentCalendarService::class)->getEvents($studentIds);

        $todayClasses = $this->getTodayClassesForStudents($filteredStudents, $parent->school);

        $school = $parent->school;

        return response()->json([
            'parent' => [
                'id' => $parent->id,
                'name' => $user->name,
                'phone' => $parent->phone,
            ],
            'school' => $school ? [
                'id' => $school->id,
                'name' => $school->name,
                'makeup_class_enabled' => (bool) $school->makeup_class_enabled,
            ] : null,
            'stats' => $stats,
            'students' => $students->map(fn($s) => [
                'id' => $s->id,
                'first_name' => $s->first_name,
                'last_name' => $s->last_name,
                'class' => $s->classModel ? ['id' => $s->classModel->id, 'name' => $s->classModel->name] : null,
            ])->values(),
            'upcoming_fees' => $upcomingFees->map(fn($f) => [
                'id' => $f->id,
                'student' => ['id' => $f->student->id, 'name' => trim($f->student->first_name . ' ' . $f->student->last_name)],
                'fee_label' => $f->fee_label,
                'amount' => (float) $f->amount,
                'due_date' => $f->due_date?->format('Y-m-d'),
            ])->values(),
            'recent_attendances' => $recentAttendances->map(fn($a) => [
                'id' => $a->id,
                'student' => $a->student ? ['id' => $a->student->id, 'name' => trim($a->student->first_name . ' ' . $a->student->last_name)] : null,
                'class' => $a->classModel ? ['id' => $a->classModel->id, 'name' => $a->classModel->name] : null,
                'attendance_date' => $a->attendance_date?->format('Y-m-d'),
                'status' => $a->status,
            ])->values(),
            'recent_progress' => $recentProgress->map(fn($p) => [
                'id' => $p->id,
                'student' => $p->student ? ['id' => $p->student->id, 'name' => trim($p->student->first_name . ' ' . $p->student->last_name)] : null,
                'class' => $p->classModel ? ['id' => $p->classModel->id, 'name' => $p->classModel->name] : null,
                'coach' => $p->coach?->user ? ['name' => $p->coach->user->name] : null,
                'progress_date' => $p->progress_date?->format('Y-m-d'),
                'notes' => $p->notes,
            ])->values(),
            'recent_media' => $recentMedia->map(function ($m) {
                $baseUrl = rtrim(request()->getSchemeAndHttpHost() . request()->getBasePath(), '/');
                return [
                    'id' => $m->id,
                    'title' => $m->title,
                    'description' => $m->description,
                    'type' => $m->type,
                    'file_url' => $m->file_path ? media_url($m->file_path) : null,
                    'file_url_secure' => $m->file_path ? $baseUrl . '/api/parent/media/' . $m->id . '/file' : null,
                    'created_at' => $m->created_at?->toIso8601String(),
                ];
            })->values(),
            'calendar_events' => $calendarEvents,
            'today_classes' => $todayClasses,
            'today_date' => Carbon::now()->toDateString(),
        ]);
    }

    /**
     * Seçili öğrencilerin bugünkü derslerini (normal + telafi) döndürür.
     */
    private function getTodayClassesForStudents($students, $school)
    {
        $today = Carbon::now()->toDateString();
        $todayDayName = strtolower(Carbon::now()->format('l'));
        $todayClasses = [];
        $studentIds = $students->pluck('id')->all();

        // Normal dersler
        foreach ($students as $student) {
            $class = $student->classModel;
            if (!$class || !$class->class_days || !in_array($todayDayName, $class->class_days)) {
                continue;
            }
            $schedule = $class->class_schedule[$todayDayName] ?? null;
            if (!$schedule || !is_array($schedule)) {
                continue;
            }
            $startTime = $schedule['start_time'] ?? null;
            $endTime = $schedule['end_time'] ?? null;
            if (!$startTime) {
                continue;
            }
            $cancelled = ClassCancellation::where('original_date', $today)
                ->where('class_id', $class->id)
                ->exists();
            if ($cancelled) {
                continue;
            }
            $studentName = trim($student->first_name . ' ' . $student->last_name) ?: 'Öğrenci';
            $todayClasses[] = [
                'type' => 'class',
                'id' => $class->id,
                'name' => $class->name,
                'student' => ['id' => $student->id, 'name' => $studentName],
                'start_time' => $startTime,
                'end_time' => $endTime ?? $startTime,
            ];
        }

        // Telafi dersleri (okulda aktifse)
        if ($school && $school->makeup_class_enabled && !empty($studentIds)) {
            $makeups = StudentMakeupClass::whereIn('student_id', $studentIds)
                ->whereNotNull('makeup_session_id')
                ->where('status', 'scheduled')
                ->whereHas('makeupSession', fn ($q) => $q->whereDate('scheduled_date', $today))
                ->with(['student', 'makeupSession'])
                ->get();

            foreach ($makeups as $smc) {
                $session = $smc->makeupSession;
                if (!$session) {
                    continue;
                }
                $startTime = $session->start_time instanceof \DateTimeInterface
                    ? $session->start_time->format('H:i')
                    : Carbon::parse($session->start_time)->format('H:i');
                $endTime = $session->end_time instanceof \DateTimeInterface
                    ? $session->end_time->format('H:i')
                    : Carbon::parse($session->end_time)->format('H:i');
                $student = $smc->student;
                $studentName = $student ? trim($student->first_name . ' ' . $student->last_name) : 'Öğrenci';
                $todayClasses[] = [
                    'type' => 'makeup',
                    'id' => $session->id,
                    'name' => $session->name ?? 'Telafi Dersi',
                    'student' => ['id' => $student?->id, 'name' => $studentName ?: 'Öğrenci'],
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ];
            }
        }

        usort($todayClasses, fn ($a, $b) => strcmp($a['start_time'], $b['start_time']));

        return $todayClasses;
    }

    private function calculateAttendanceRate($studentIds)
    {
        $total = Attendance::whereIn('student_id', $studentIds)->count();
        $present = Attendance::whereIn('student_id', $studentIds)->where('status', 'present')->count();
        return $total > 0 ? round(($present / $total) * 100, 1) : 0;
    }
}
