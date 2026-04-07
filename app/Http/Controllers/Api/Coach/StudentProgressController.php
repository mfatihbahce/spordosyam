<?php

namespace App\Http\Controllers\Api\Coach;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Coach;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentProgressController extends Controller
{
    public function index()
    {
        $coach = Coach::where('user_id', Auth::id())->first();
        if (!$coach) {
            return response()->json(['message' => 'Antrenör bulunamadı.'], 404);
        }

        $progresses = StudentProgress::where('coach_id', $coach->id)
            ->with(['student', 'classModel'])
            ->orderBy('progress_date', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'progress' => $progresses->map(fn ($p) => $this->formatProgress($p)),
        ]);
    }

    public function store(Request $request)
    {
        $coach = Coach::where('user_id', Auth::id())->first();
        if (!$coach) {
            return response()->json(['message' => 'Antrenör bulunamadı.'], 404);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'progress_date' => 'required|date',
            'notes' => 'nullable|string',
            'progress_type' => 'nullable|in:skill,attitude,physical,general',
        ]);

        $validated['coach_id'] = $coach->id;
        $progress = StudentProgress::create($validated);

        return response()->json([
            'message' => 'Gelişim notu başarıyla eklendi.',
            'progress' => $this->formatProgress($progress->load(['student', 'classModel'])),
        ], 201);
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
        ];
    }
}
