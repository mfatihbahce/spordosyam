<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index()
    {
        $parent = Auth::user()->parent;

        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        $students = $parent->students()
            ->with(['classModel.sportBranch', 'classModel.coach.user', 'school', 'currentEnrollments.classModel.coach.user'])
            ->get();

        return response()->json([
            'students' => $students->map(function ($s) {
                $class = $s->classModel;
                if (!$class && $s->currentEnrollments->isNotEmpty()) {
                    $class = $s->currentEnrollments->first()?->classModel;
                }
                return [
                    'id' => $s->id,
                    'first_name' => $s->first_name,
                    'last_name' => $s->last_name,
                    'gender' => $s->gender,
                    'class' => $class ? [
                        'id' => $class->id,
                        'name' => $class->name,
                        'branch' => $class->sportBranch->name ?? null,
                        'coach' => $class->coach && $class->coach->user ? [
                            'id' => $class->coach->id,
                            'name' => $class->coach->user->name,
                        ] : null,
                    ] : null,
                    'school' => $s->school ? [
                        'id' => $s->school->id,
                        'name' => $s->school->name,
                    ] : null,
                ];
            })->values(),
        ]);
    }
}

