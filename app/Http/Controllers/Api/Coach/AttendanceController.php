<?php

namespace App\Http\Controllers\Api\Coach;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassCancellation;
use App\Models\ClassModel;
use App\Models\Coach;
use App\Models\MakeupSession;
use App\Models\Student;
use App\Models\StudentClassHistory;
use App\Models\StudentMakeupClass;
use App\Models\MakeupClass;
use App\Services\SmsNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Bugünkü yoklama alınabilecek dersler (web create sayfasındaki todayCards mantığı).
     */
    public function todayClasses(Request $request)
    {
        $coach = Coach::where('user_id', Auth::id())->first();
        if (!$coach) {
            return response()->json(['message' => 'Antrenör bulunamadı.'], 404);
        }

        $date = $request->query('date', now()->toDateString());
        $today = Carbon::parse($date)->toDateString();
        $todayDayName = strtolower(Carbon::parse($date)->format('l'));
        $now = Carbon::now();

        $classes = ClassModel::where('coach_id', $coach->id)
            ->where('school_id', $coach->school_id)
            ->active()
            ->get();

        $school = $coach->school;
        $makeupClassEnabled = $school ? $school->makeup_class_enabled : false;

        $cancelledClassIdsToday = ClassCancellation::where('original_date', $today)
            ->whereIn('class_id', $classes->pluck('id'))
            ->pluck('class_id')
            ->all();

        $classIdsWithAttendanceToday = Attendance::where('coach_id', $coach->id)
            ->where('attendance_date', $today)
            ->whereNotNull('class_id')
            ->distinct()
            ->pluck('class_id')
            ->all();

        $makeupSessionIdsWithAttendanceToday = Attendance::where('coach_id', $coach->id)
            ->where('attendance_date', $today)
            ->whereNotNull('makeup_session_id')
            ->distinct()
            ->pluck('makeup_session_id')
            ->all();

        $todayCards = [];
        foreach ($classes as $class) {
            if (in_array($class->id, $classIdsWithAttendanceToday) || in_array($class->id, $cancelledClassIdsToday)) {
                continue;
            }
            if (!$class->class_days || !in_array($todayDayName, $class->class_days)) {
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
            $startDt = Carbon::parse($today . ' ' . $startTime);
            $opensAt = $startDt->copy()->subMinutes(15);
            $canTake = $now->format('H:i') >= $opensAt->format('H:i');
            $todayCards[] = [
                'type' => 'class',
                'id' => $class->id,
                'name' => $class->name,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'opens_at' => $opensAt->format('H:i'),
                'can_take' => $canTake,
            ];
        }
        if ($makeupClassEnabled) {
            $todaySessions = MakeupSession::where('coach_id', $coach->id)
                ->whereDate('scheduled_date', $today)
                ->withCount('studentMakeupClasses')
                ->orderBy('start_time')
                ->get();
            foreach ($todaySessions as $ms) {
                if (in_array($ms->id, $makeupSessionIdsWithAttendanceToday)) {
                    continue;
                }
                $startTime = $ms->start_time instanceof \DateTimeInterface
                    ? $ms->start_time->format('H:i')
                    : Carbon::parse($ms->start_time)->format('H:i');
                $startDt = Carbon::parse($today . ' ' . $startTime);
                $opensAt = $startDt->copy()->subMinutes(15);
                $canTake = $now->format('H:i') >= $opensAt->format('H:i');
                $todayCards[] = [
                    'type' => 'makeup',
                    'id' => $ms->id,
                    'name' => $ms->name ?? 'Telafi Dersi',
                    'start_time' => $startTime,
                    'end_time' => $ms->end_time instanceof \DateTimeInterface
                        ? $ms->end_time->format('H:i')
                        : Carbon::parse($ms->end_time)->format('H:i'),
                    'opens_at' => $opensAt->format('H:i'),
                    'can_take' => $canTake,
                ];
            }
        }
        usort($todayCards, fn ($a, $b) => strcmp($a['start_time'], $b['start_time']));

        return response()->json([
            'date' => $today,
            'today_cards' => $todayCards,
            'makeup_class_enabled' => $makeupClassEnabled,
        ]);
    }

    /**
     * Yoklama formu için öğrenci listesi (class_id veya makeup_session_id ile).
     */
    public function formStudents(Request $request)
    {
        $coach = Coach::where('user_id', Auth::id())->first();
        if (!$coach) {
            return response()->json(['message' => 'Antrenör bulunamadı.'], 404);
        }

        $classId = $request->query('class_id');
        $makeupSessionId = $request->query('makeup_session_id');
        $date = $request->query('date', now()->toDateString());

        if ($makeupSessionId) {
            $session = MakeupSession::where('id', $makeupSessionId)->where('coach_id', $coach->id)->first();
            if (!$session) {
                return response()->json(['message' => 'Telafi dersi bulunamadı.'], 404);
            }
            $students = $session->studentMakeupClasses()->with('student')->get()->map(fn ($sm) => $sm->student)->filter();
            return response()->json([
                'type' => 'makeup',
                'session' => [
                    'id' => $session->id,
                    'name' => $session->name ?? 'Telafi Dersi',
                    'scheduled_date' => $session->scheduled_date?->format('Y-m-d'),
                ],
                'students' => $students->map(fn ($s) => [
                    'id' => $s->id,
                    'first_name' => $s->first_name,
                    'last_name' => $s->last_name,
                    'name' => trim($s->first_name . ' ' . $s->last_name),
                ])->values(),
                'attendance_date' => $session->scheduled_date?->format('Y-m-d'),
            ]);
        }

        if ($classId) {
            $class = ClassModel::where('id', $classId)->where('coach_id', $coach->id)->with('students')->first();
            if (!$class) {
                return response()->json(['message' => 'Sınıf bulunamadı.'], 404);
            }
            $students = $class->students;
            return response()->json([
                'type' => 'class',
                'class' => [
                    'id' => $class->id,
                    'name' => $class->name,
                ],
                'students' => $students->map(fn ($s) => [
                    'id' => $s->id,
                    'first_name' => $s->first_name,
                    'last_name' => $s->last_name,
                    'name' => trim($s->first_name . ' ' . $s->last_name),
                ])->values(),
                'attendance_date' => $date,
            ]);
        }

        return response()->json(['message' => 'class_id veya makeup_session_id gerekli.'], 400);
    }

    public function index()
    {
        $coach = Coach::where('user_id', Auth::id())->first();
        if (!$coach) {
            return response()->json(['message' => 'Antrenör bulunamadı.'], 404);
        }

        $attendances = Attendance::where('coach_id', $coach->id)
            ->with(['student', 'classModel', 'makeupSession'])
            ->orderBy('attendance_date', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'attendances' => $attendances->map(function ($a) {
                return [
                    'id' => $a->id,
                    'date' => $a->attendance_date?->format('Y-m-d'),
                    'time' => $a->attendance_time?->format('H:i'),
                    'status' => $a->status,
                    'notes' => $a->notes,
                    'student' => $a->student ? [
                        'id' => $a->student->id,
                        'name' => trim($a->student->first_name . ' ' . $a->student->last_name),
                    ] : null,
                    'class' => $a->classModel ? [
                        'id' => $a->classModel->id,
                        'name' => $a->classModel->name,
                    ] : null,
                    'makeup_session_id' => $a->makeup_session_id,
                ];
            })->values(),
        ]);
    }

    /**
     * Yoklama kaydet (mobil). Coach\AttendanceController@store mantığının JSON versiyonu.
     */
    public function store(Request $request)
    {
        $coach = Coach::where('user_id', Auth::id())->first();
        if (!$coach) {
            return response()->json(['message' => 'Antrenör bulunamadı.'], 404);
        }

        $school = $coach->school;
        $makeupClassEnabled = $school ? $school->makeup_class_enabled : false;

        $makeupSessionId = $request->input('makeup_session_id');
        $isMakeupSession = $makeupSessionId && MakeupSession::where('id', $makeupSessionId)->where('coach_id', $coach->id)->exists();

        if ($isMakeupSession) {
            $validated = $request->validate([
                'makeup_session_id' => 'required|exists:makeup_sessions,id',
                'attendance_date' => 'required|date',
                'attendance_time' => 'nullable',
                'attendances' => 'required|array',
                'attendances.*.student_id' => 'required|exists:students,id',
                'attendances.*.status' => 'required|in:present,absent',
                'attendances.*.notes' => 'nullable|string',
            ]);
            $session = MakeupSession::findOrFail($validated['makeup_session_id']);
            $validated['attendance_date'] = $session->scheduled_date->format('Y-m-d');
        } else {
            $validated = $request->validate([
                'class_id' => 'required|exists:classes,id',
                'attendance_date' => 'required|date',
                'attendance_time' => 'nullable',
                'attendances' => 'required|array',
                'attendances.*.student_id' => 'required|exists:students,id',
                'attendances.*.status' => 'required|in:present,absent' . ($makeupClassEnabled ? ',excused' : ''),
                'attendances.*.notes' => 'nullable|string',
            ]);
        }

        DB::beginTransaction();
        try {
            foreach ($validated['attendances'] as $attendanceData) {
                $attendance = Attendance::create([
                    'student_id' => $attendanceData['student_id'],
                    'class_id' => $isMakeupSession ? null : $validated['class_id'],
                    'makeup_session_id' => $isMakeupSession ? $validated['makeup_session_id'] : null,
                    'coach_id' => $coach->id,
                    'attendance_date' => $validated['attendance_date'],
                    'attendance_time' => $validated['attendance_time'] ?? now()->format('H:i:s'),
                    'status' => $attendanceData['status'],
                    'notes' => $attendanceData['notes'] ?? null,
                ]);

                $student = Student::find($attendanceData['student_id']);

                if ($student && $attendanceData['status'] === 'absent') {
                    $parent = $student->parents()->first();
                    if ($parent && !empty($parent->phone)) {
                        $dateStr = \Carbon\Carbon::parse($validated['attendance_date'])->format('d.m.Y');
                        $msg = "{$student->first_name} {$student->last_name} {$dateStr} derse katilmadi. Spordosyam";
                        app(SmsNotificationService::class)->sendIfEnabled('attendance_absent', $parent->phone, $msg, $parent->user);
                    }
                }

                if ($isMakeupSession) {
                    continue;
                }

                if ($makeupClassEnabled && $attendanceData['status'] === 'excused') {
                    $this->createMakeupClassForExcusedStudent($student, $attendance, $coach->school_id);
                    continue;
                }
                if ($attendanceData['status'] === 'present' || $attendanceData['status'] === 'absent') {
                    $enrollment = StudentClassHistory::where('student_id', $student->id)
                        ->where('class_id', $validated['class_id'])
                        ->whereNull('left_at')
                        ->first();
                    if ($enrollment) {
                        $enrollment->increment('used_credits');
                    }
                }
            }

            DB::commit();

            return response()->json(['message' => 'Yoklama başarıyla kaydedildi.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Yoklama kaydedilirken bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    private function createMakeupClassForExcusedStudent(Student $student, Attendance $attendance, $schoolId)
    {
        $makeupClass = MakeupClass::create([
            'school_id' => $schoolId,
            'original_class_id' => $attendance->class_id,
            'type' => 'excused',
            'status' => 'pending',
        ]);

        StudentMakeupClass::create([
            'student_id' => $student->id,
            'makeup_class_id' => $makeupClass->id,
            'attendance_id' => $attendance->id,
            'status' => 'pending',
        ]);
    }
}

