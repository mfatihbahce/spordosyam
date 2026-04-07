<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index()
    {
        $parent = Auth::user()->parent;
        
        if (!$parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Veli bilgileriniz bulunamadı.');
        }

        $students = $parent->students()->with(['classModel.sportBranch', 'school', 'currentEnrollments.classModel'])->get();

        return view('parent.student.index', compact('students'));
    }
}
