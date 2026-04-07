<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\ClassCancellation;
use App\Models\ClassModel;
use App\Models\Coach;
use App\Models\MakeupSession;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $coach = Coach::where('user_id', $user->id)->first();
        
        if (!$coach) {
            return view('coach.dashboard', [
                'classes' => collect(),
                'totalStudents' => 0,
                'todayClasses' => collect(),
                'stats' => [],
                'recent_attendances' => collect(),
                'recent_progress' => collect(),
            ])->with('error', 'Antrenör bilgileriniz bulunamadı.');
        }

        $classes = ClassModel::where('coach_id', $coach->id)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->withCount('students')
            ->with(['sportBranch', 'branch'])
            ->get();

        $classIds = $classes->pluck('id');
        $totalStudents = Student::whereIn('class_id', $classIds)->count();

        // Bugünkü dersler - bugünün gününe göre; iptal edilen dersler hariç
        $todayDayName = strtolower(now()->format('l'));
        $todayDate = now()->toDateString();
        $cancelledClassIdsToday = ClassCancellation::where('original_date', $todayDate)
            ->whereIn('class_id', ClassModel::where('coach_id', $coach->id)->pluck('id'))
            ->pluck('class_id')
            ->all();
        $todayClasses = ClassModel::where('coach_id', $coach->id)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->whereJsonContains('class_days', $todayDayName)
            ->whereNotIn('id', $cancelledClassIdsToday)
            ->with(['sportBranch', 'students'])
            ->get();

        $stats = [
            'total_classes' => $classes->count(),
            'total_students' => $totalStudents,
            'total_attendances' => Attendance::where('coach_id', $coach->id)
                ->where('status', 'present')
                ->count(),
            'monthly_attendances' => Attendance::where('coach_id', $coach->id)
                ->where('status', 'present')
                ->whereMonth('attendance_date', now()->month)
                ->whereYear('attendance_date', now()->year)
                ->count(),
            'total_progress' => \App\Models\StudentProgress::where('coach_id', $coach->id)->count(),
        ];

        $recent_attendances = Attendance::where('coach_id', $coach->id)
            ->with(['student', 'classModel'])
            ->orderBy('attendance_date', 'desc')
            ->limit(10)
            ->get();

        $recent_progress = \App\Models\StudentProgress::where('coach_id', $coach->id)
            ->with(['student', 'classModel'])
            ->orderBy('progress_date', 'desc')
            ->limit(5)
            ->get();

        // Bugünkü telafi dersleri (antrenöre atanan)
        $todayMakeupSessions = MakeupSession::where('coach_id', $coach->id)
            ->whereDate('scheduled_date', now()->toDateString())
            ->withCount('studentMakeupClasses')
            ->orderBy('start_time')
            ->get();

        // Takvim verileri - Antrenöre atanan dersler
        $calendarEvents = $this->getCalendarEvents($coach->id);

        return view('coach.dashboard', compact('classes', 'totalStudents', 'todayClasses', 'todayMakeupSessions', 'stats', 'recent_attendances', 'recent_progress', 'calendarEvents'));
    }

    private function getCalendarEvents($coachId)
    {
        $coach = Coach::find($coachId);
        $classIds = $coach ? ClassModel::where('coach_id', $coachId)->pluck('id')->all() : [];
        $cancelledSet = collect(ClassCancellation::whereIn('class_id', $classIds)->get())
            ->map(fn ($c) => $c->class_id . ':' . $c->original_date->format('Y-m-d'))
            ->values()
            ->all();

        $classes = ClassModel::where('coach_id', $coachId)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->with(['sportBranch', 'branch', 'students'])
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
                        $shortClassName = mb_strlen($class->name) > 18 ? mb_substr($class->name, 0, 18) . '...' : $class->name;
                        $events[] = [
                            'title' => $timeOnly . ' ' . $shortClassName,
                            'start' => $startDateTime,
                            'end' => $endDateTime,
                            'color' => $this->getClassColor($class->id),
                            'extendedProps' => [
                                'class_id' => $class->id,
                                'sport' => $class->sportBranch->name ?? '',
                                'branch' => $class->branch->name ?? '',
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

        $makeupSessions = MakeupSession::where('coach_id', $coachId)
            ->where('scheduled_date', '>=', now()->toDateString())
            ->where('scheduled_date', '<=', now()->addMonths(3)->toDateString())
            ->with(['branch'])
            ->get();
        foreach ($makeupSessions as $session) {
            $d = $session->scheduled_date->format('Y-m-d');
            $st = $session->start_time instanceof \DateTimeInterface
                ? $session->start_time->format('H:i')
                : \Carbon\Carbon::parse($session->start_time)->format('H:i');
            $et = $session->end_time instanceof \DateTimeInterface
                ? $session->end_time->format('H:i')
                : \Carbon\Carbon::parse($session->end_time)->format('H:i');
            $name = $session->name ?? 'Telafi Dersi';
            $events[] = [
                'title' => $st . ' Telafi: ' . (mb_strlen($name) > 15 ? mb_substr($name, 0, 15) . '...' : $name),
                'start' => $d . ' ' . $st,
                'end' => $d . ' ' . $et,
                'color' => '#8B5CF6',
                'extendedProps' => [
                    'makeup_session_id' => $session->id,
                    'full_class_name' => $name,
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
}
