<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $parent = Auth::user()->parent;

        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        $studentIds = $parent->students->pluck('id');

        $attendances = Attendance::whereIn('student_id', $studentIds)
            ->with(['student', 'classModel', 'coach'])
            ->orderBy('attendance_date', 'desc')
            ->limit(50)
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
                    'coach' => $a->coach ? [
                        'id' => $a->coach->id,
                        'name' => $a->coach->user->name ?? null,
                    ] : null,
                ];
            })->values(),
        ]);
    }
}

