<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\StudentProgress;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentProgressController extends Controller
{
    public function index()
    {
        $coach = Auth::user()->coach;
        
        if (!$coach) {
            return redirect()->route('coach.dashboard')->with('error', 'Antrenör bilgileriniz bulunamadı.');
        }

        $progresses = StudentProgress::where('coach_id', $coach->id)
            ->with(['student', 'classModel'])
            ->orderBy('progress_date', 'desc')
            ->paginate(15);

        return view('coach.student-progress.index', compact('progresses'));
    }

    public function create()
    {
        $coach = Auth::user()->coach;
        
        if (!$coach) {
            return redirect()->route('coach.dashboard')->with('error', 'Antrenör bilgileriniz bulunamadı.');
        }

        $classes = ClassModel::where('coach_id', $coach->id)->with('students')->get();
        return view('coach.student-progress.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $coach = Auth::user()->coach;
        
        if (!$coach) {
            return redirect()->route('coach.dashboard')->with('error', 'Antrenör bilgileriniz bulunamadı.');
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'progress_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['coach_id'] = $coach->id;
        StudentProgress::create($validated);

        return redirect()->route('coach.student-progress.index')
            ->with('success', 'Gelişim notu başarıyla eklendi.');
    }
}
