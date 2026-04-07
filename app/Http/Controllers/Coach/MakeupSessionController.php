<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\MakeupSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MakeupSessionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $coach = Coach::where('user_id', $user->id)->first();

        if (!$coach) {
            abort(404, 'Antrenör bilgileriniz bulunamadı.');
        }

        $school = $coach->school;
        if (!$school || !$school->makeup_class_enabled) {
            abort(403, 'Telafi dersi özelliği aktif değil.');
        }

        $sessions = MakeupSession::where('coach_id', $coach->id)
            ->where('scheduled_date', '>=', now()->toDateString())
            ->with(['branch'])
            ->withCount('studentMakeupClasses')
            ->orderBy('scheduled_date')
            ->orderBy('start_time')
            ->paginate(15);

        return view('coach.makeup-sessions.index', compact('sessions'));
    }
}
