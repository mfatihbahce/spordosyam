<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\Media;
use App\Models\MakeupSession;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $coach = Auth::user()->coach;

        if (!$coach) {
            return redirect()->route('coach.dashboard')->with('error', 'Antrenör bilgileriniz bulunamadı.');
        }

        $coachId = $coach->id;

        // Temel sayılar
        $totalAttendances = Attendance::where('coach_id', $coachId)->count();
        $presentCount = Attendance::where('coach_id', $coachId)->where('status', 'present')->count();
        $absentCount = Attendance::where('coach_id', $coachId)->where('status', 'absent')->count();
        $attendanceRate = $totalAttendances > 0
            ? round(($presentCount / $totalAttendances) * 100, 1)
            : 0;

        $totalProgress = StudentProgress::where('coach_id', $coachId)->count();
        $totalClasses = ClassModel::where('coach_id', $coachId)->active()->count();
        $classIds = ClassModel::where('coach_id', $coachId)->pluck('id');
        $totalStudents = \App\Models\Student::whereIn('class_id', $classIds)->count();

        // Paylaşım (coach'ın yüklediği medya)
        $totalMedia = Media::where('school_id', $coach->school_id)
            ->where('uploaded_by', Auth::id())
            ->where('uploader_type', 'coach')
            ->count();

        // Telafi dersleri (planlanmış)
        $totalMakeupSessions = MakeupSession::where('coach_id', $coachId)->count();
        $upcomingMakeupSessions = MakeupSession::where('coach_id', $coachId)
            ->where('scheduled_date', '>=', now()->toDateString())
            ->count();

        // Sınıfa göre yoklama özeti
        $attendanceByClass = Attendance::where('coach_id', $coachId)
            ->select('class_id', DB::raw('count(*) as total'), DB::raw("sum(case when status = 'present' then 1 else 0 end) as present_count"))
            ->groupBy('class_id')
            ->get();

        $classIdsWithAttendance = $attendanceByClass->pluck('class_id')->unique()->filter();
        $classesMap = ClassModel::whereIn('id', $classIdsWithAttendance)->get()->keyBy('id');

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

        // Sınıfa göre gelişim notu sayısı
        $progressByClass = StudentProgress::where('coach_id', $coachId)
            ->select('class_id', DB::raw('count(*) as cnt'))
            ->groupBy('class_id')
            ->get();
        $progressClassIds = $progressByClass->pluck('class_id')->unique()->filter();
        $progressClassesMap = ClassModel::whereIn('id', $progressClassIds)->get()->keyBy('id');
        $progressByClassList = $progressByClass->map(function ($row) use ($progressClassesMap) {
            $classModel = $progressClassesMap->get($row->class_id);
            return [
                'class_name' => $classModel ? $classModel->name : '—',
                'count' => (int) $row->cnt,
            ];
        });

        // Son 7 gün günlük katılım (grafik için)
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $present = Attendance::where('coach_id', $coachId)
                ->where('attendance_date', $date)
                ->where('status', 'present')
                ->count();
            $absent = Attendance::where('coach_id', $coachId)
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

        // Son yoklamalar
        $recentAttendances = Attendance::where('coach_id', $coachId)
            ->with(['student', 'classModel'])
            ->orderBy('attendance_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(15)
            ->get();

        // Son gelişim notları
        $recentProgress = StudentProgress::where('coach_id', $coachId)
            ->with(['student', 'classModel'])
            ->orderBy('progress_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $stats = [
            'total_attendances' => $totalAttendances,
            'present_count' => $presentCount,
            'absent_count' => $absentCount,
            'attendance_rate' => $attendanceRate,
            'total_progress' => $totalProgress,
            'total_classes' => $totalClasses,
            'total_students' => $totalStudents,
            'total_media' => $totalMedia,
            'total_makeup_sessions' => $totalMakeupSessions,
            'upcoming_makeup_sessions' => $upcomingMakeupSessions,
        ];

        return view('coach.reports.index', [
            'stats' => $stats,
            'attendanceByClassList' => $attendanceByClassList,
            'progressByClassList' => $progressByClassList,
            'last7Days' => $last7Days,
            'recentAttendances' => $recentAttendances,
            'recentProgress' => $recentProgress,
        ]);
    }
}
