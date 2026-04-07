<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use App\Models\ParentModel;
use App\Models\StudentMakeupClass;
use Illuminate\Support\Facades\Auth;

class MakeupSessionController extends Controller
{
    public function index()
    {
        $parent = Auth::user()->parent;
        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        $studentIds = $parent->students()->pluck('students.id')->all();
        $school = $parent->school;

        if (!$school || !$school->makeup_class_enabled) {
            return response()->json([
                'message' => 'Telafi dersi özelliği aktif değil.',
                'makeups' => [],
            ]);
        }

        $makeups = StudentMakeupClass::whereIn('student_id', $studentIds)
            ->whereNotNull('makeup_session_id')
            ->where('status', 'scheduled')
            ->whereHas('makeupSession', fn($q) => $q->where('scheduled_date', '>=', now()->toDateString()))
            ->with(['student', 'makeupSession.coach.user', 'makeupSession.branch'])
            ->join('makeup_sessions', 'makeup_sessions.id', '=', 'student_makeup_classes.makeup_session_id')
            ->orderBy('makeup_sessions.scheduled_date')
            ->orderBy('makeup_sessions.start_time')
            ->select('student_makeup_classes.*')
            ->limit(50)
            ->get();

        return response()->json([
            'makeups' => $makeups->map(function ($m) {
                $session = $m->makeupSession;
                return [
                    'id' => $m->id,
                    'student' => $m->student ? [
                        'id' => $m->student->id,
                        'name' => trim($m->student->first_name . ' ' . $m->student->last_name),
                    ] : null,
                    'scheduled_date' => $session?->scheduled_date?->format('Y-m-d'),
                    'start_time' => $session?->start_time?->format('H:i'),
                    'end_time' => $session?->end_time?->format('H:i'),
                    'branch' => $session?->branch ? ['id' => $session->branch->id, 'name' => $session->branch->name] : null,
                    'coach' => $session?->coach?->user ? ['name' => $session->coach->user->name] : null,
                ];
            })->values(),
        ]);
    }
}
