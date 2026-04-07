<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassCancellation;
use App\Models\ClassModel;
use App\Models\Coach;
use App\Models\MakeupClass;
use App\Models\MakeupSession;
use App\Models\Student;
use App\Models\StudentClassHistory;
use App\Models\StudentMakeupClass;
use App\Services\SmsNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $coach = Coach::where('user_id', $user->id)->first();
        
        if (!$coach) {
            return redirect()->route('coach.dashboard')
                ->with('error', 'Antrenör bilgileriniz bulunmamaktadır.');
        }

        $attendances = Attendance::where('coach_id', $coach->id)
            ->with(['student', 'classModel', 'makeupSession'])
            ->orderBy('attendance_date', 'desc')
            ->paginate(15);

        return view('coach.attendances.index', compact('attendances'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $coach = Coach::where('user_id', $user->id)->first();

        if (!$coach) {
            return redirect()->route('coach.dashboard')
                ->with('error', 'Antrenör bilgileriniz bulunmamaktadır.');
        }

        $school = $coach->school;
        $makeupClassEnabled = $school ? $school->makeup_class_enabled : false;
        $today = now()->toDateString();
        $todayDayName = strtolower(now()->format('l'));
        $now = now();

        $preselectedClassId = $request->query('class_id');
        $preselectedMakeupSessionId = $request->query('makeup_session_id');
        $preselectedDate = $request->query('date', $today);
        $formMode = ($preselectedClassId && $preselectedDate) || ($preselectedMakeupSessionId && $preselectedDate);

        $classes = ClassModel::where('coach_id', $coach->id)
            ->where('is_active', true)
            ->with('students')
            ->get();

        $makeupSessions = collect([]);
        $makeupSessionsForJs = [];
        if ($makeupClassEnabled) {
            $makeupSessions = MakeupSession::where('coach_id', $coach->id)
                ->where('scheduled_date', '<=', now()->toDateString())
                ->with(['studentMakeupClasses.student'])
                ->orderBy('scheduled_date', 'desc')
                ->orderBy('start_time')
                ->limit(50)
                ->get();
            $makeupSessionsForJs = $makeupSessions->keyBy('id')->map(function ($ms) {
                return [
                    'id' => $ms->id,
                    'name' => $ms->name ?? 'Telafi Dersi',
                    'scheduled_date' => $ms->scheduled_date->format('Y-m-d'),
                    'start_time' => \Carbon\Carbon::parse($ms->start_time)->format('H:i'),
                    'students' => $ms->studentMakeupClasses->map(fn ($sm) => $sm->student)->values()->all(),
                ];
            })->all();
        }

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
            if (in_array($class->id, $classIdsWithAttendanceToday)) {
                continue;
            }
            if (in_array($class->id, $cancelledClassIdsToday)) {
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
            $startDt = \Carbon\Carbon::parse($today . ' ' . $startTime);
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
                    : \Carbon\Carbon::parse($ms->start_time)->format('H:i');
                $startDt = \Carbon\Carbon::parse($today . ' ' . $startTime);
                $opensAt = $startDt->copy()->subMinutes(15);
                $canTake = $now->format('H:i') >= $opensAt->format('H:i');
                $todayCards[] = [
                    'type' => 'makeup',
                    'id' => $ms->id,
                    'name' => $ms->name ?? 'Telafi Dersi',
                    'start_time' => $startTime,
                    'end_time' => $ms->end_time instanceof \DateTimeInterface
                        ? $ms->end_time->format('H:i')
                        : \Carbon\Carbon::parse($ms->end_time)->format('H:i'),
                    'opens_at' => $opensAt->format('H:i'),
                    'can_take' => $canTake,
                ];
            }
        }
        usort($todayCards, function ($a, $b) {
            return strcmp($a['start_time'], $b['start_time']);
        });

        $formStudents = [];
        $formIsMakeup = false;
        if ($formMode) {
            if ($preselectedClassId) {
                $preselectedClass = $classes->firstWhere('id', (int) $preselectedClassId);
                if ($preselectedClass) {
                    $formStudents = $preselectedClass->students->all();
                }
            } elseif ($preselectedMakeupSessionId) {
                $formIsMakeup = true;
                $session = $makeupSessions->firstWhere('id', (int) $preselectedMakeupSessionId);
                if ($session) {
                    $formStudents = $session->studentMakeupClasses->map(fn ($sm) => $sm->student)->all();
                }
            }
        }

        return view('coach.attendances.create', compact(
            'classes', 'makeupClassEnabled', 'makeupSessions', 'makeupSessionsForJs',
            'formMode', 'preselectedClassId', 'preselectedMakeupSessionId', 'preselectedDate',
            'todayCards', 'today', 'formStudents', 'formIsMakeup'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $coach = Coach::where('user_id', $user->id)->first();
        
        if (!$coach) {
            return redirect()->route('coach.dashboard')
                ->with('error', 'Antrenör bilgileriniz bulunmamaktadır.');
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
                    // Telafi dersi yoklaması: sadece kayıt, ders hakkı/izinli mantığı yok
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
            
            return redirect()->route('coach.attendances.index')
                ->with('success', 'Yoklama başarıyla kaydedildi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Yoklama kaydedilirken bir hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * İzinli öğrenci için telafi dersi oluştur
     */
    private function createMakeupClassForExcusedStudent(Student $student, Attendance $attendance, $schoolId)
    {
        // MakeupClass oluştur (izinli öğrenci için)
        $makeupClass = MakeupClass::create([
            'school_id' => $schoolId,
            'original_class_id' => $attendance->class_id,
            'type' => 'excused',
            'status' => 'pending',
        ]);
        
        // StudentMakeupClass oluştur
        StudentMakeupClass::create([
            'student_id' => $student->id,
            'makeup_class_id' => $makeupClass->id,
            'attendance_id' => $attendance->id,
            'status' => 'pending',
        ]);
    }
}
