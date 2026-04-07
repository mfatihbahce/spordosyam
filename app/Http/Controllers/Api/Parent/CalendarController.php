<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use App\Models\ParentModel;
use App\Services\ParentCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    /**
     * Veli öğrencilerinin ders takvimi.
     * Normal dersler + telafi dersleri. İptal edilen dersler hariç.
     *
     * Query params (opsiyonel):
     * - start_date: Y-m-d (varsayılan: bu hafta başı)
     * - end_date: Y-m-d (varsayılan: +3 ay)
     */
    public function index(Request $request)
    {
        $parent = Auth::user()->parent;

        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        $students = $parent->students;
        $studentIds = $students->pluck('id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $events = app(ParentCalendarService::class)->getEvents($studentIds, $startDate, $endDate);

        return response()->json([
            'events' => $events,
            'students' => $students->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->first_name . ' ' . $s->last_name,
            ])->values(),
        ]);
    }
}
