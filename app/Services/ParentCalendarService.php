<?php

namespace App\Services;

use App\Models\ClassCancellation;
use App\Models\Student;
use App\Models\StudentMakeupClass;
use Carbon\Carbon;

class ParentCalendarService
{
    /**
     * Veli öğrencilerinin ders takvimini döner.
     * Normal dersler + telafi dersleri. İptal edilen dersler hariç.
     *
     * @param  \Illuminate\Support\Collection|array  $studentIds
     * @param  string|null  $startDate  Y-m-d (varsayılan: bu hafta başı)
     * @param  string|null  $endDate  Y-m-d (varsayılan: +3 ay)
     */
    public function getEvents($studentIds, ?string $startDate = null, ?string $endDate = null): array
    {
        $studentIds = collect($studentIds)->filter()->unique()->values()->all();
        if (empty($studentIds)) {
            return [];
        }

        $start = $startDate ? Carbon::parse($startDate)->startOfWeek() : now()->startOfWeek();
        $end = $endDate ? Carbon::parse($endDate)->endOfWeek() : now()->addMonths(3)->endOfWeek();

        $students = Student::whereIn('id', $studentIds)
            ->whereNotNull('class_id')
            ->with(['classModel.sportBranch', 'classModel.branch', 'classModel.coach.user'])
            ->get();

        $classIds = $students->pluck('class_id')->filter()->unique()->all();
        $cancelledSet = collect(ClassCancellation::whereIn('class_id', $classIds)->get())
            ->map(fn($c) => $c->class_id . ':' . $c->original_date->format('Y-m-d'))
            ->values()
            ->all();

        $events = [];
        $dayNames = [
            'monday' => 'Pazartesi', 'tuesday' => 'Salı', 'wednesday' => 'Çarşamba',
            'thursday' => 'Perşembe', 'friday' => 'Cuma', 'saturday' => 'Cumartesi', 'sunday' => 'Pazar',
        ];

        foreach ($students as $student) {
            $class = $student->classModel;
            if (!$class || !$class->class_days || !$class->class_schedule) continue;
            if ($class->end_date && $class->end_date < $start->toDateString()) continue;
            if (!$class->is_active) continue;

            $maxEndDate = $class->end_date ? min($end, Carbon::parse($class->end_date)) : $end;

            foreach ($class->class_days as $day) {
                $schedule = $class->class_schedule[$day] ?? null;
                if (!$schedule) continue;
                $startTime = is_array($schedule) ? ($schedule['start_time'] ?? null) : $schedule;
                $endTime = is_array($schedule) ? ($schedule['end_time'] ?? null) : null;
                if (!$startTime) continue;

                $dayNumber = $this->getDayNumber($day);
                $currentDate = $start->copy();

                while ($currentDate->lte($maxEndDate)) {
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
                            ],
                        ];
                    }
                    $currentDate->addDay();
                }
            }
        }

        $makeupRecords = StudentMakeupClass::whereIn('student_id', $studentIds)
            ->whereNotNull('makeup_session_id')
            ->where('status', 'scheduled')
            ->whereHas('makeupSession', fn($q) => $q->where('scheduled_date', '>=', $start->toDateString())
                ->where('scheduled_date', '<=', $end->toDateString()))
            ->with(['student', 'makeupSession.coach.user', 'makeupSession.branch'])
            ->get();

        foreach ($makeupRecords as $sm) {
            $session = $sm->makeupSession;
            if (!$session) continue;
            $d = $session->scheduled_date->format('Y-m-d');
            $st = $session->start_time instanceof \DateTimeInterface
                ? $session->start_time->format('H:i')
                : Carbon::parse($session->start_time)->format('H:i');
            $et = $session->end_time instanceof \DateTimeInterface
                ? $session->end_time->format('H:i')
                : Carbon::parse($session->end_time)->format('H:i');
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
                ],
            ];
        }

        usort($events, fn($a, $b) => strcmp($a['start'], $b['start']));

        return $events;
    }

    private function getDayNumber(string $day): int
    {
        $days = ['monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6, 'sunday' => 0];
        return $days[$day] ?? 1;
    }

    private function getClassColor(int $classId): string
    {
        $colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#F97316', '#84CC16', '#6366F1', '#14B8A6', '#F43F5E', '#A855F7', '#0EA5E9', '#22C55E'];
        return $colors[($classId - 1) % count($colors)];
    }
}
