<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $schoolId = Auth::user()->school_id;
        
        $attendances = Attendance::with(['student', 'classModel', 'coach'])
            ->whereHas('student', function($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->orderBy('attendance_date', 'desc')
            ->paginate(20);
        
        // İstatistikler
        $stats = [
            'total' => Attendance::whereHas('student', function($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })->count(),
            'present' => Attendance::whereHas('student', function($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })->where('status', 'present')->count(),
            'absent' => Attendance::whereHas('student', function($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })->where('status', 'absent')->count(),
        ];
        
        return view('admin.attendances.index', compact('attendances', 'stats'));
    }
}
