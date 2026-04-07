<?php

namespace App\Http\Controllers\Api\Coach;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Coach;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    public function index()
    {
        $coach = Coach::where('user_id', Auth::id())->first();
        if (!$coach) {
            return response()->json(['message' => 'Antrenör bulunamadı.'], 404);
        }

        $classes = ClassModel::where('coach_id', $coach->id)
            ->where('school_id', $coach->school_id)
            ->active()
            ->withCount('students')
            ->get();

        return response()->json([
            'classes' => $classes->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'students_count' => $c->students_count,
                ];
            })->values(),
        ]);
    }

    /**
     * Sınıf detayı (öğrenci sayısı, program, vb.).
     */
    public function show(int $id)
    {
        $coach = Coach::where('user_id', Auth::id())->first();
        if (!$coach) {
            return response()->json(['message' => 'Antrenör bulunamadı.'], 404);
        }

        $class = ClassModel::where('id', $id)
            ->where('coach_id', $coach->id)
            ->withCount('students')
            ->with(['sportBranch', 'branch', 'students'])
            ->first();

        if (!$class) {
            return response()->json(['message' => 'Sınıf bulunamadı.'], 404);
        }

        return response()->json([
            'class' => [
                'id' => $class->id,
                'name' => $class->name,
                'description' => $class->description,
                'capacity' => $class->capacity,
                'students_count' => $class->students_count,
                'class_days' => $class->class_days,
                'class_schedule' => $class->class_schedule,
                'end_date' => $class->end_date?->format('Y-m-d'),
                'sport_branch' => $class->sportBranch ? [
                    'id' => $class->sportBranch->id,
                    'name' => $class->sportBranch->name,
                ] : null,
                'branch' => $class->branch ? [
                    'id' => $class->branch->id,
                    'name' => $class->branch->name,
                ] : null,
                'students' => $class->students->map(fn ($s) => [
                    'id' => $s->id,
                    'first_name' => $s->first_name,
                    'last_name' => $s->last_name,
                    'name' => trim($s->first_name . ' ' . $s->last_name),
                ])->values(),
            ],
        ]);
    }

    /**
     * Sınıf öğrencileri (yoklama formu, gelişim notu formu vb. için).
     */
    public function students(int $id)
    {
        $coach = Coach::where('user_id', Auth::id())->first();
        if (!$coach) {
            return response()->json(['message' => 'Antrenör bulunamadı.'], 404);
        }

        $class = ClassModel::where('id', $id)
            ->where('coach_id', $coach->id)
            ->with('students')
            ->first();

        if (!$class) {
            return response()->json(['message' => 'Sınıf bulunamadı.'], 404);
        }

        return response()->json([
            'class' => [
                'id' => $class->id,
                'name' => $class->name,
            ],
            'students' => $class->students->map(fn ($s) => [
                'id' => $s->id,
                'first_name' => $s->first_name,
                'last_name' => $s->last_name,
                'name' => trim($s->first_name . ' ' . $s->last_name),
            ])->values(),
        ]);
    }
}

