<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    public function index()
    {
        $coach = Auth::user()->coach;
        
        if (!$coach) {
            return redirect()->route('coach.dashboard')->with('error', 'Antrenör bilgileriniz bulunamadı.');
        }

        $classes = ClassModel::where('coach_id', $coach->id)
            ->with(['sportBranch', 'branch', 'students'])
            ->withCount('students')
            ->paginate(15);

        return view('coach.classes.index', compact('classes'));
    }
}
