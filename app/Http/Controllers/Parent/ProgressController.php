<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function index()
    {
        $parent = Auth::user()->parent;
        
        if (!$parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Veli bilgileriniz bulunamadı.');
        }

        $studentIds = $parent->students->pluck('id');
        
        $progresses = StudentProgress::whereIn('student_id', $studentIds)
            ->with(['student', 'classModel', 'coach'])
            ->orderBy('progress_date', 'desc')
            ->paginate(20);

        return view('parent.progress.index', compact('progresses'));
    }

    public function show(StudentProgress $progress)
    {
        $parent = Auth::user()->parent;

        if (!$parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Veli bilgileriniz bulunamadı.');
        }

        if (!$parent->students->contains($progress->student_id)) {
            abort(403, 'Bu gelişim notu size ait değil.');
        }

        $progress->load(['student', 'classModel', 'coach.user']);

        return view('parent.progress.show', compact('progress'));
    }
}
