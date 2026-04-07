<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use App\Models\StudentProgress;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function index()
    {
        $parent = Auth::user()->parent;

        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        $studentIds = $parent->students->pluck('id');

        $progresses = StudentProgress::whereIn('student_id', $studentIds)
            ->with(['student', 'classModel', 'coach'])
            ->orderBy('progress_date', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'progress' => $progresses->map(fn ($p) => $this->formatProgress($p)),
        ]);
    }

    public function show(StudentProgress $progress)
    {
        $parent = Auth::user()->parent;

        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        if (!$parent->students->contains($progress->student_id)) {
            return response()->json(['message' => 'Bu gelişim notu size ait değil.'], 403);
        }

        $progress->load(['student', 'classModel', 'coach.user']);

        return response()->json($this->formatProgress($progress));
    }

    private function formatProgress(StudentProgress $p): array
    {
        $typeLabels = [
            'skill' => 'Teknik Beceri',
            'attitude' => 'Davranış',
            'physical' => 'Fiziksel Gelişim',
            'general' => 'Genel',
        ];

        return [
            'id' => $p->id,
            'date' => $p->progress_date?->format('Y-m-d'),
            'notes' => $p->notes,
            'progress_type' => $p->progress_type,
            'progress_type_label' => $typeLabels[$p->progress_type ?? 'general'] ?? 'Genel',
            'student' => $p->student ? [
                'id' => $p->student->id,
                'name' => trim($p->student->first_name . ' ' . $p->student->last_name),
            ] : null,
            'class' => $p->classModel ? [
                'id' => $p->classModel->id,
                'name' => $p->classModel->name,
            ] : null,
            'coach' => $p->coach ? [
                'id' => $p->coach->id,
                'name' => $p->coach->user->name ?? null,
            ] : null,
        ];
    }
}

