<?php

namespace App\Http\Controllers\Api\Coach;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\MakeupSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MakeupSessionController extends Controller
{
    public function index()
    {
        $coach = Coach::where('user_id', Auth::id())->first();
        if (!$coach) {
            return response()->json(['message' => 'Antrenör bulunamadı.'], 404);
        }

        $school = $coach->school;
        if (!$school || !$school->makeup_class_enabled) {
            return response()->json([
                'message' => 'Telafi dersi özelliği aktif değil.',
                'sessions' => [],
            ]);
        }

        $sessions = MakeupSession::where('coach_id', $coach->id)
            ->where('scheduled_date', '>=', now()->toDateString())
            ->with(['branch'])
            ->withCount('studentMakeupClasses')
            ->orderBy('scheduled_date')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'sessions' => $sessions->map(function ($s) {
                $startTime = $s->start_time instanceof \DateTimeInterface
                    ? $s->start_time->format('H:i')
                    : ($s->start_time ? Carbon::parse($s->start_time)->format('H:i') : null);
                $endTime = $s->end_time instanceof \DateTimeInterface
                    ? $s->end_time->format('H:i')
                    : ($s->end_time ? Carbon::parse($s->end_time)->format('H:i') : null);
                return [
                    'id' => $s->id,
                    'name' => $s->name ?? ('Telafi Dersi - ' . $s->scheduled_date?->format('d.m.Y') . ' ' . $startTime),
                    'scheduled_date' => $s->scheduled_date?->format('Y-m-d'),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'branch' => $s->branch ? ['id' => $s->branch->id, 'name' => $s->branch->name] : null,
                    'students_count' => $s->student_makeup_classes_count ?? 0,
                ];
            })->values(),
        ]);
    }

    public function show(int $id)
    {
        $coach = Coach::where('user_id', Auth::id())->first();
        if (!$coach) {
            return response()->json(['message' => 'Antrenör bulunamadı.'], 404);
        }

        $session = MakeupSession::where('id', $id)
            ->where('coach_id', $coach->id)
            ->with(['branch', 'studentMakeupClasses.student'])
            ->first();

        if (!$session) {
            return response()->json(['message' => 'Telafi dersi bulunamadı.'], 404);
        }

        $startTime = $session->start_time instanceof \DateTimeInterface
            ? $session->start_time->format('H:i')
            : ($session->start_time ? Carbon::parse($session->start_time)->format('H:i') : null);
        $endTime = $session->end_time instanceof \DateTimeInterface
            ? $session->end_time->format('H:i')
            : ($session->end_time ? Carbon::parse($session->end_time)->format('H:i') : null);

        return response()->json([
            'session' => [
                'id' => $session->id,
                'name' => $session->name ?? ('Telafi Dersi - ' . $session->scheduled_date?->format('d.m.Y') . ' ' . $startTime),
                'scheduled_date' => $session->scheduled_date?->format('Y-m-d'),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'branch' => $session->branch ? ['id' => $session->branch->id, 'name' => $session->branch->name] : null,
                'students' => $session->studentMakeupClasses->map(fn ($sm) => [
                    'id' => $sm->student?->id,
                    'name' => $sm->student ? trim($sm->student->first_name . ' ' . $sm->student->last_name) : null,
                ])->filter(fn ($s) => $s['name'] != null)->values(),
            ],
        ]);
    }
}
