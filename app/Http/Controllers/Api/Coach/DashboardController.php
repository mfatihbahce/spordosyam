<?php

namespace App\Http\Controllers\Api\Coach;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassCancellation;
use App\Models\ClassModel;
use App\Models\Coach;
use App\Models\MakeupSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Antrenör dashboard özet verileri (mobil için sade JSON).
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        $coach = Coach::where('user_id', $user->id)->first();

        if (!$coach) {
            return response()->json([
                'message' => 'Antrenör bulunamadı.',
            ], 404);
        }

        $classes = ClassModel::where('coach_id', $coach->id)
            ->where('school_id', $coach->school_id)
            ->active()
            ->withCount('students')
            ->get();

        $classIds = $classes->pluck('id');

        $totalClasses = $classes->count();
        $totalAttendances = Attendance::whereIn('class_id', $classIds)->count();
        $presentAttendances = Attendance::whereIn('class_id', $classIds)
            ->where('status', 'present')
            ->count();

        $attendanceRate = $totalAttendances > 0
            ? round(($presentAttendances / $totalAttendances) * 100, 1)
            : 0.0;

        $today = Carbon::now()->toDateString();
        $todayDayName = strtolower(Carbon::now()->format('l'));
        $now = Carbon::now();

        $school = $coach->school;
        $makeupClassEnabled = $school ? $school->makeup_class_enabled : false;

        $cancelledClassIdsToday = ClassCancellation::where('original_date', $today)
            ->whereIn('class_id', $classIds)
            ->pluck('class_id')
            ->all();

        $classIdsWithAttendanceToday = Attendance::where('coach_id', $coach->id)
            ->where('attendance_date', $today)
            ->whereNotNull('class_id')
            ->distinct()
            ->pluck('class_id')
            ->all();

        $todayClasses = [];
        foreach ($classes as $class) {
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
            $startDt = Carbon::parse($today . ' ' . $startTime);
            $opensAt = $startDt->copy()->subMinutes(15);
            $canTake = $now->format('H:i') >= $opensAt->format('H:i');
            $attendanceTaken = in_array($class->id, $classIdsWithAttendanceToday);

            $attendanceSummary = null;
            if ($attendanceTaken) {
                $atts = Attendance::where('coach_id', $coach->id)
                    ->where('attendance_date', $today)
                    ->where('class_id', $class->id)
                    ->get();
                $attendanceSummary = [
                    'present' => $atts->where('status', 'present')->count(),
                    'absent' => $atts->where('status', 'absent')->count(),
                    'excused' => $atts->where('status', 'excused')->count(),
                ];
            }

            $todayClasses[] = [
                'type' => 'class',
                'id' => $class->id,
                'name' => $class->name,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'opens_at' => $opensAt->format('H:i'),
                'can_take' => $canTake,
                'attendance_taken' => $attendanceTaken,
                'attendance_summary' => $attendanceSummary,
            ];
        }

        if ($makeupClassEnabled) {
            $todaySessions = MakeupSession::where('coach_id', $coach->id)
                ->whereDate('scheduled_date', $today)
                ->withCount('studentMakeupClasses')
                ->orderBy('start_time')
                ->get();

            $makeupSessionIdsWithAttendance = Attendance::where('coach_id', $coach->id)
                ->where('attendance_date', $today)
                ->whereNotNull('makeup_session_id')
                ->distinct()
                ->pluck('makeup_session_id')
                ->all();

            foreach ($todaySessions as $ms) {
                $startTime = $ms->start_time instanceof \DateTimeInterface
                    ? $ms->start_time->format('H:i')
                    : Carbon::parse($ms->start_time)->format('H:i');
                $startDt = Carbon::parse($today . ' ' . $startTime);
                $opensAt = $startDt->copy()->subMinutes(15);
                $canTake = $now->format('H:i') >= $opensAt->format('H:i');
                $attendanceTaken = in_array($ms->id, $makeupSessionIdsWithAttendance);

                $attendanceSummary = null;
                if ($attendanceTaken) {
                    $atts = Attendance::where('coach_id', $coach->id)
                        ->where('attendance_date', $today)
                        ->where('makeup_session_id', $ms->id)
                        ->get();
                    $attendanceSummary = [
                        'present' => $atts->where('status', 'present')->count(),
                        'absent' => $atts->where('status', 'absent')->count(),
                        'excused' => $atts->where('status', 'excused')->count(),
                    ];
                }

                $todayClasses[] = [
                    'type' => 'makeup',
                    'id' => $ms->id,
                    'name' => $ms->name ?? 'Telafi Dersi',
                    'start_time' => $startTime,
                    'end_time' => $ms->end_time instanceof \DateTimeInterface
                        ? $ms->end_time->format('H:i')
                        : Carbon::parse($ms->end_time)->format('H:i'),
                    'opens_at' => $opensAt->format('H:i'),
                    'can_take' => $canTake,
                    'attendance_taken' => $attendanceTaken,
                    'attendance_summary' => $attendanceSummary,
                ];
            }
        }

        usort($todayClasses, fn ($a, $b) => strcmp($a['start_time'], $b['start_time']));

        return response()->json([
            'coach' => [
                'id' => $coach->id,
                'name' => $user->name,
                'phone' => $coach->phone,
            ],
            'stats' => [
                'total_classes' => $totalClasses,
                'total_attendances' => $totalAttendances,
                'attendance_rate' => $attendanceRate,
            ],
            'today_classes' => $todayClasses,
            'today_date' => $today,
            'makeup_class_enabled' => $makeupClassEnabled,
            'classes' => $classes->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'students_count' => $c->students_count,
                ];
            })->values(),
        ]);
    }
}

