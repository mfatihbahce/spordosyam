<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $parent = Auth::user()->parent;
        
        if (!$parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Veli bilgileriniz bulunamadı.');
        }

        $studentIds = $parent->students->pluck('id');
        
        $attendances = Attendance::whereIn('student_id', $studentIds)
            ->with(['student', 'classModel', 'coach'])
            ->orderBy('attendance_date', 'desc')
            ->paginate(20);

        return view('parent.attendances.index', compact('attendances'));
    }
}
