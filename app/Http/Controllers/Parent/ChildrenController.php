<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChildrenController extends Controller
{
    public function index()
    {
        $parent = Auth::user()->parent;
        
        if (!$parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Veli bilgileriniz bulunamadı.');
        }

        $students = $parent->students()->with(['classModel', 'school'])->get();

        return view('parent.children.index', compact('students'));
    }
}
