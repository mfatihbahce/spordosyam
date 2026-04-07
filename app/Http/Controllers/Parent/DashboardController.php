<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\ClassCancellation;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\StudentMakeupClass;
use App\Models\Attendance;
use App\Models\Media;
use App\Models\StudentFee;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();
        
        if (!$parent) {
            return view('parent.dashboard', [
                'stats' => [
                    'total_children' => 0,
                    'attendance_rate' => 0,
                    'pending_fees' => 0,
                    'recent_media' => collect(),
                ],
                'students' => collect(),
                'recent_attendances' => collect(),
            ])->with('error', 'Veli bilgileriniz bulunamadı.');
        }

        $students = $parent->students()->get();
        $studentIds = $students->pluck('id');

        $totalAttendances = Attendance::whereIn('student_id', $studentIds)->count();
        $presentAttendances = Attendance::whereIn('student_id', $studentIds)
            ->where('status', 'present')
            ->count();
        $absentAttendances = Attendance::whereIn('student_id', $studentIds)
            ->where('status', 'absent')
            ->count();

        $stats = [
            'total_children' => $students->count(),
            'attendance_rate' => $this->calculateAttendanceRate($studentIds),
            'total_attendances' => $totalAttendances,
            'present_attendances' => $presentAttendances,
            'absent_attendances' => $absentAttendances,
            'pending_fees' => StudentFee::whereIn('student_id', $studentIds)
                ->where('status', 'pending')
                ->sum('amount'),
            'total_paid' => Payment::whereIn('student_fee_id', StudentFee::whereIn('student_id', $studentIds)->pluck('id'))
                ->where('status', 'completed')
                ->sum('amount'),
            'recent_media' => Media::whereHas('targets', function($query) use ($studentIds) {
                $query->where('target_type', 'student')
                    ->whereIn('target_id', $studentIds);
            })->orWhereHas('targets', function($query) use ($students) {
                $query->where('target_type', 'class')
                    ->whereIn('target_id', $students->pluck('class_id')->filter());
            })->orderBy('created_at', 'desc')->limit(5)->get(),
            'total_progress' => \App\Models\StudentProgress::whereIn('student_id', $studentIds)->count(),
        ];

        $recent_attendances = Attendance::whereIn('student_id', $studentIds)
            ->orderBy('attendance_date', 'desc')
            ->limit(10)
            ->with(['student', 'classModel', 'coach'])
            ->get();

        $upcoming_fees = StudentFee::whereIn('student_id', $studentIds)
            ->where('status', 'pending')
            ->where('due_date', '<=', now()->addDays(7))
            ->with(['student', 'feePlan'])
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        $recent_progress = \App\Models\StudentProgress::whereIn('student_id', $studentIds)
            ->with(['student', 'classModel', 'coach'])
            ->orderBy('progress_date', 'desc')
            ->limit(5)
            ->get();

        // Takvim verileri - Öğrencilerin dersleri
        $calendarEvents = $this->getCalendarEvents($studentIds);

        return view('parent.dashboard', compact('stats', 'students', 'recent_attendances', 'upcoming_fees', 'recent_progress', 'calendarEvents'));
    }

    private function getCalendarEvents($studentIds)
    {
        $students = Student::whereIn('id', $studentIds)
            ->whereNotNull('class_id')
            ->with(['classModel.sportBranch', 'classModel.branch', 'classModel.coach.user'])
            ->get();

        $classIds = $students->pluck('class_id')->filter()->unique()->all();
        $cancelledSet = collect(ClassCancellation::whereIn('class_id', $classIds)->get())
            ->map(fn ($c) => $c->class_id . ':' . $c->original_date->format('Y-m-d'))
            ->values()
            ->all();

        $events = [];
        $dayNames = [
            'monday' => 'Pazartesi',
            'tuesday' => 'Salı',
            'wednesday' => 'Çarşamba',
            'thursday' => 'Perşembe',
            'friday' => 'Cuma',
            'saturday' => 'Cumartesi',
            'sunday' => 'Pazar'
        ];

        foreach ($students as $student) {
            $class = $student->classModel;
            if (!$class || !$class->class_days || !$class->class_schedule) {
                continue;
            }
            if ($class->end_date && $class->end_date < now()->toDateString()) {
                continue;
            }
            if (!$class->is_active) {
                continue;
            }

            foreach ($class->class_days as $day) {
                $schedule = $class->class_schedule[$day] ?? null;
                if (!$schedule) {
                    continue;
                }
                $startTime = is_array($schedule) ? ($schedule['start_time'] ?? null) : $schedule;
                $endTime = is_array($schedule) ? ($schedule['end_time'] ?? null) : null;
                if (!$startTime) {
                    continue;
                }

                $startDate = now()->startOfWeek();
                $maxEndDate = $class->end_date ? min(now()->addMonths(3)->endOfWeek(), $class->end_date) : now()->addMonths(3)->endOfWeek();
                $endDate = $maxEndDate;
                $dayNumber = $this->getDayNumber($day);

                $currentDate = $startDate->copy();
                while ($currentDate->lte($endDate)) {
                    if ($currentDate->dayOfWeek === $dayNumber) {
                        $eventDate = $currentDate->format('Y-m-d');
                        if (in_array($class->id . ':' . $eventDate, $cancelledSet)) {
                            $currentDate->addDay();
                            continue;
                        }
                        $startDateTime = $eventDate . ' ' . $startTime;
                        $endDateTime = $endTime ? ($eventDate . ' ' . $endTime) : null;
                        $timeOnly = date('H:i', strtotime($startTime));
                        $shortClassName = mb_strlen($class->name) > 15 ? mb_substr($class->name, 0, 15) . '...' : $class->name;
                        $shortStudentName = mb_strlen($student->first_name) > 8 ? mb_substr($student->first_name, 0, 8) . '...' : $student->first_name;
                        $events[] = [
                            'title' => $timeOnly . ' ' . $shortClassName . ' - ' . $shortStudentName,
                            'start' => $startDateTime,
                            'end' => $endDateTime,
                            'color' => $this->getClassColor($class->id),
                            'extendedProps' => [
                                'class_id' => $class->id,
                                'student_id' => $student->id,
                                'student_name' => $student->first_name . ' ' . $student->last_name,
                                'sport' => $class->sportBranch->name ?? '',
                                'branch' => $class->branch->name ?? '',
                                'coach' => $class->coach->user->name ?? '',
                                'day' => $dayNames[$day] ?? $day,
                                'full_class_name' => $class->name,
                                'is_makeup' => false,
                            ]
                        ];
                    }
                    $currentDate->addDay();
                }
            }
        }

        $makeupRecords = StudentMakeupClass::whereIn('student_id', $studentIds)
            ->whereNotNull('makeup_session_id')
            ->where('status', 'scheduled')
            ->whereHas('makeupSession', fn ($q) => $q->where('scheduled_date', '>=', now()->toDateString())
                ->where('scheduled_date', '<=', now()->addMonths(3)->toDateString()))
            ->with(['student', 'makeupSession.coach.user', 'makeupSession.branch'])
            ->get();
        foreach ($makeupRecords as $sm) {
            $session = $sm->makeupSession;
            if (!$session) {
                continue;
            }
            $d = $session->scheduled_date->format('Y-m-d');
            $st = $session->start_time instanceof \DateTimeInterface
                ? $session->start_time->format('H:i')
                : \Carbon\Carbon::parse($session->start_time)->format('H:i');
            $et = $session->end_time instanceof \DateTimeInterface
                ? $session->end_time->format('H:i')
                : \Carbon\Carbon::parse($session->end_time)->format('H:i');
            $name = $session->name ?? 'Telafi Dersi';
            $studentName = $sm->student->first_name . ' ' . $sm->student->last_name;
            $events[] = [
                'title' => $st . ' Telafi - ' . $studentName,
                'start' => $d . ' ' . $st,
                'end' => $d . ' ' . $et,
                'color' => '#8B5CF6',
                'extendedProps' => [
                    'makeup_session_id' => $session->id,
                    'student_id' => $sm->student_id,
                    'student_name' => $studentName,
                    'full_class_name' => $name,
                    'coach' => $session->coach->user->name ?? '',
                    'branch' => $session->branch->name ?? '',
                    'is_makeup' => true,
                ]
            ];
        }

        return $events;
    }

    private function getDayNumber($day)
    {
        // Carbon dayOfWeek: 0=Sunday, 1=Monday, ..., 6=Saturday
        $days = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 0
        ];
        return $days[$day] ?? 1;
    }

    /**
     * Sınıf ID'sine göre tutarlı renk atama
     */
    private function getClassColor($classId)
    {
        $colors = [
            '#3B82F6', // Mavi
            '#10B981', // Yeşil
            '#F59E0B', // Turuncu
            '#EF4444', // Kırmızı
            '#8B5CF6', // Mor
            '#EC4899', // Pembe
            '#06B6D4', // Cyan
            '#F97316', // Turuncu-Kırmızı
            '#84CC16', // Açık Yeşil
            '#6366F1', // İndigo
            '#14B8A6', // Teal
            '#F43F5E', // Rose
            '#A855F7', // Mor
            '#0EA5E9', // Sky Blue
            '#22C55E', // Yeşil
        ];
        
        return $colors[($classId - 1) % count($colors)];
    }

    private function calculateAttendanceRate($studentIds)
    {
        $total = Attendance::whereIn('student_id', $studentIds)->count();
        $present = Attendance::whereIn('student_id', $studentIds)
            ->where('status', 'present')
            ->count();

        return $total > 0 ? round(($present / $total) * 100, 1) : 0;
    }
}
