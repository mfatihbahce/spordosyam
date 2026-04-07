<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassCancellation;
use App\Models\ClassModel;
use App\Models\Coach;
use App\Models\MakeupSession;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $schoolId = Auth::user()->school_id;

        $stats = [
            'active_students' => Student::where('school_id', $schoolId)->where('is_active', true)->count(),
            'total_students' => Student::where('school_id', $schoolId)->count(),
            'monthly_revenue' => Payment::where('school_id', $schoolId)
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'total_revenue' => Payment::where('school_id', $schoolId)
                ->where('status', 'completed')
                ->sum('amount'),
            'active_classes' => ClassModel::where('school_id', $schoolId)
                ->where('is_active', true)
                ->where(function($q) {
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>=', now()->toDateString());
                })
                ->count(),
            'total_classes' => ClassModel::where('school_id', $schoolId)->count(),
            'total_coaches' => Coach::where('school_id', $schoolId)->where('is_active', true)->count(),
            'total_parents' => \App\Models\ParentModel::where('school_id', $schoolId)->where('is_active', true)->count(),
            'pending_fees' => \App\Models\StudentFee::whereHas('student', function($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })->where('status', 'pending')->sum('amount'),
            'total_attendances' => \App\Models\Attendance::whereHas('student', function($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })->where('status', 'present')->count(),
        ];

        $recent_payments = Payment::where('school_id', $schoolId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->with(['studentFee.student', 'parent.user'])
            ->get();

        $recent_students = Student::where('school_id', $schoolId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->with('classModel')
            ->get();

        $upcoming_fees = \App\Models\StudentFee::whereHas('student', function($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->where('status', 'pending')
            ->where('due_date', '<=', now()->addDays(7))
            ->with(['student', 'feePlan'])
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get();

        // Takvim verileri - Tüm okulun dersleri
        $calendarEvents = $this->getCalendarEvents($schoolId);

        return view('admin.dashboard', compact('stats', 'recent_payments', 'recent_students', 'upcoming_fees', 'calendarEvents'));
    }

    private function getCalendarEvents($schoolId)
    {
        $school = \App\Models\School::find($schoolId);
        $cancelledSet = collect(ClassCancellation::where('school_id', $schoolId)->get())
            ->map(fn ($c) => $c->class_id . ':' . $c->original_date->format('Y-m-d'))
            ->values()
            ->all();

        $classes = ClassModel::where('school_id', $schoolId)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->with(['sportBranch', 'branch', 'coach.user'])
            ->get();

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

        foreach ($classes as $class) {
            if (!$class->class_days || !$class->class_schedule) {
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
                        $shortClassName = mb_strlen($class->name) > 20 ? mb_substr($class->name, 0, 20) . '...' : $class->name;
                        $events[] = [
                            'title' => $timeOnly . ' ' . $shortClassName,
                            'start' => $startDateTime,
                            'end' => $endDateTime,
                            'color' => $this->getClassColor($class->id),
                            'extendedProps' => [
                                'class_id' => $class->id,
                                'sport' => $class->sportBranch->name ?? '',
                                'branch' => $class->branch->name ?? '',
                                'coach' => $class->coach->user->name ?? '',
                                'students' => $class->students->count(),
                                'capacity' => $class->capacity,
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

        if ($school && $school->makeup_class_enabled) {
            $sessions = MakeupSession::where('school_id', $schoolId)
                ->where('scheduled_date', '>=', now()->toDateString())
                ->where('scheduled_date', '<=', now()->addMonths(3)->toDateString())
                ->with(['coach.user', 'branch'])
                ->get();
            foreach ($sessions as $session) {
                $d = $session->scheduled_date->format('Y-m-d');
                $st = $session->start_time instanceof \DateTimeInterface
                    ? $session->start_time->format('H:i')
                    : \Carbon\Carbon::parse($session->start_time)->format('H:i');
                $et = $session->end_time instanceof \DateTimeInterface
                    ? $session->end_time->format('H:i')
                    : \Carbon\Carbon::parse($session->end_time)->format('H:i');
                $name = $session->name ?? 'Telafi Dersi';
                $events[] = [
                    'title' => $st . ' Telafi: ' . (mb_strlen($name) > 18 ? mb_substr($name, 0, 18) . '...' : $name),
                    'start' => $d . ' ' . $st,
                    'end' => $d . ' ' . $et,
                    'color' => '#8B5CF6',
                    'extendedProps' => [
                        'makeup_session_id' => $session->id,
                        'full_class_name' => $name,
                        'coach' => $session->coach->user->name ?? '',
                        'branch' => $session->branch->name ?? '',
                        'is_makeup' => true,
                    ]
                ];
            }
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
        
        // Sınıf ID'sine göre renk seç (tutarlılık için)
        return $colors[($classId - 1) % count($colors)];
    }

    private function getEventColor($students, $capacity)
    {
        if ($capacity == 0) return '#6B7280'; // Gri
        $ratio = $students / $capacity;
        if ($ratio >= 0.9) return '#EF4444'; // Kırmızı - Dolu
        if ($ratio >= 0.7) return '#F59E0B'; // Turuncu - Yoğun
        if ($ratio >= 0.5) return '#3B82F6'; // Mavi - Orta
        return '#10B981'; // Yeşil - Az
    }
}
