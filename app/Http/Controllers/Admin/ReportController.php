<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\Coach;
use App\Models\ParentModel;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $schoolId = Auth::user()->school_id;

        // Temel sayılar
        $totalStudents = Student::where('school_id', $schoolId)->count();
        $activeStudents = Student::where('school_id', $schoolId)->where('is_active', true)->count();
        $totalPayments = Payment::where('school_id', $schoolId)->where('status', 'completed')->sum('amount');
        $monthlyPayments = Payment::where('school_id', $schoolId)
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        $totalAttendances = Attendance::whereHas('student', fn($q) => $q->where('school_id', $schoolId))
            ->where('status', 'present')
            ->count();
        $absentCount = Attendance::whereHas('student', fn($q) => $q->where('school_id', $schoolId))
            ->where('status', 'absent')
            ->count();
        $allAttendanceCount = Attendance::whereHas('student', fn($q) => $q->where('school_id', $schoolId))->count();
        $attendanceRate = $allAttendanceCount > 0
            ? round((($totalAttendances) / $allAttendanceCount) * 100, 1)
            : 0;

        $activeClasses = ClassModel::where('school_id', $schoolId)->active()->count();
        $totalClasses = ClassModel::where('school_id', $schoolId)->count();
        $totalCoaches = Coach::where('school_id', $schoolId)->where('is_active', true)->count();
        $totalParents = ParentModel::where('school_id', $schoolId)->where('is_active', true)->count();

        $pendingFees = StudentFee::whereHas('student', fn($q) => $q->where('school_id', $schoolId))
            ->whereIn('status', ['pending', 'overdue'])
            ->sum('amount');
        $pendingFeesCount = StudentFee::whereHas('student', fn($q) => $q->where('school_id', $schoolId))
            ->whereIn('status', ['pending', 'overdue'])
            ->count();

        // Sınıfa göre yoklama özeti
        $classIds = ClassModel::where('school_id', $schoolId)->pluck('id');
        $attendanceByClass = Attendance::whereIn('class_id', $classIds)
            ->select('class_id', DB::raw('count(*) as total'), DB::raw("sum(case when status = 'present' then 1 else 0 end) as present_count"))
            ->groupBy('class_id')
            ->get();
        $classesMap = ClassModel::whereIn('id', $attendanceByClass->pluck('class_id')->unique()->filter())->get()->keyBy('id');
        $attendanceByClassList = $attendanceByClass->map(function ($row) use ($classesMap) {
            $classModel = $classesMap->get($row->class_id);
            $present = (int) $row->present_count;
            $total = (int) $row->total;
            return [
                'class_name' => $classModel ? $classModel->name : '—',
                'present' => $present,
                'absent' => $total - $present,
                'total' => $total,
                'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            ];
        });

        // Son 7 gün katılım
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $present = Attendance::whereHas('student', fn($q) => $q->where('school_id', $schoolId))
                ->where('attendance_date', $date)
                ->where('status', 'present')
                ->count();
            $absent = Attendance::whereHas('student', fn($q) => $q->where('school_id', $schoolId))
                ->where('attendance_date', $date)
                ->where('status', 'absent')
                ->count();
            $last7Days->push([
                'date' => $date,
                'label' => now()->subDays($i)->locale('tr')->translatedFormat('d M'),
                'present' => $present,
                'absent' => $absent,
                'total' => $present + $absent,
            ]);
        }

        // Son ödemeler
        $recentPayments = Payment::where('school_id', $schoolId)
            ->with(['studentFee.student', 'parent.user'])
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();

        // Son yoklamalar
        $recentAttendances = Attendance::whereHas('student', fn($q) => $q->where('school_id', $schoolId))
            ->with(['student', 'classModel'])
            ->orderBy('attendance_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(15)
            ->get();

        // Yaklaşan aidatlar (7 gün)
        $upcomingFees = StudentFee::whereHas('student', fn($q) => $q->where('school_id', $schoolId))
            ->whereIn('status', ['pending', 'overdue'])
            ->where('due_date', '<=', now()->addDays(7))
            ->with('student')
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get();

        $stats = [
            'total_students' => $totalStudents,
            'active_students' => $activeStudents,
            'total_payments' => $totalPayments,
            'monthly_payments' => $monthlyPayments,
            'total_attendances' => $totalAttendances,
            'absent_count' => $absentCount,
            'attendance_rate' => $attendanceRate,
            'active_classes' => $activeClasses,
            'total_classes' => $totalClasses,
            'total_coaches' => $totalCoaches,
            'total_parents' => $totalParents,
            'pending_fees' => $pendingFees,
            'pending_fees_count' => $pendingFeesCount,
        ];

        return view('admin.reports.index', [
            'stats' => $stats,
            'attendanceByClassList' => $attendanceByClassList,
            'last7Days' => $last7Days,
            'recentPayments' => $recentPayments,
            'recentAttendances' => $recentAttendances,
            'upcomingFees' => $upcomingFees,
        ]);
    }
}
