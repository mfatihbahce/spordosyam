<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\ParentModel;
use App\Models\StudentMakeupClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MakeupSessionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        if (!$parent) {
            abort(404, 'Veli bilgileriniz bulunamadı.');
        }

        $studentIds = $parent->students()->pluck('students.id')->all();
        $school = $parent->school;
        if (!$school || !$school->makeup_class_enabled) {
            abort(403, 'Telafi dersi özelliği aktif değil.');
        }

        $makeups = StudentMakeupClass::whereIn('student_id', $studentIds)
            ->whereNotNull('makeup_session_id')
            ->where('status', 'scheduled')
            ->whereHas('makeupSession', fn ($q) => $q->where('scheduled_date', '>=', now()->toDateString()))
            ->with(['student', 'makeupSession.coach.user', 'makeupSession.branch'])
            ->join('makeup_sessions', 'makeup_sessions.id', '=', 'student_makeup_classes.makeup_session_id')
            ->orderBy('makeup_sessions.scheduled_date')
            ->orderBy('makeup_sessions.start_time')
            ->select('student_makeup_classes.*')
            ->paginate(15);

        return view('parent.makeup-sessions.index', compact('makeups'));
    }
}
