<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentMakeupClass;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentMakeupClassController extends Controller
{
    /**
     * Telafi bekleyen öğrenciler listesi
     */
    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $school = Auth::user()->school;
        
        // Telafi dersi verilmiyorsa erişimi engelle
        if (!$school || !$school->makeup_class_enabled) {
            abort(403, 'Telafi dersi özelliği aktif değil.');
        }
        
        // Telafi bekleyen öğrenciler (pending status)
        $studentMakeupClasses = StudentMakeupClass::whereHas('student', function($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->where('status', 'pending')
            ->with(['student', 'makeupClass', 'attendance'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.student-makeup-classes.index', compact('studentMakeupClasses'));
    }

    /**
     * Öğrenci için telafi dersi tarihi belirle
     */
    public function update(Request $request, $id)
    {
        $schoolId = Auth::user()->school_id;
        
        $studentMakeupClass = StudentMakeupClass::findOrFail($id);
        
        // Öğrencinin bu okula ait olduğunu kontrol et
        if ($studentMakeupClass->student->school_id !== $schoolId) {
            abort(403);
        }
        
        $validated = $request->validate([
            'scheduled_class_id' => 'nullable|exists:classes,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
        ]);
        
        // Eğer var olan bir ders seçildiyse, o dersin bu okula ait olduğunu kontrol et
        if ($validated['scheduled_class_id']) {
            $scheduledClass = ClassModel::where('id', $validated['scheduled_class_id'])
                ->where('school_id', $schoolId)
                ->firstOrFail();
        }
        
        $studentMakeupClass->update([
            'scheduled_class_id' => $validated['scheduled_class_id'] ?? null,
            'scheduled_date' => $validated['scheduled_date'],
            'status' => 'scheduled',
        ]);
        
        // MakeupClass'ı da güncelle
        $makeupClass = $studentMakeupClass->makeupClass;
        $makeupClass->update([
            'scheduled_class_id' => $validated['scheduled_class_id'] ?? null,
            'scheduled_date' => $validated['scheduled_date'],
            'status' => 'scheduled',
        ]);
        
        return redirect()->route('admin.student-makeup-classes.index')
            ->with('success', 'Telafi dersi tarihi belirlendi.');
    }

    /**
     * Belirli bir tarihte dersleri getir (AJAX için)
     */
    public function getClassesByDate(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $date = $request->input('date');
        
        if (!$date) {
            return response()->json(['classes' => []]);
        }
        
        $carbonDate = \Carbon\Carbon::parse($date);
        $dayName = strtolower($carbonDate->format('l')); // monday, tuesday, etc.
        
        // O gün için aktif dersleri bul
        $classes = ClassModel::where('school_id', $schoolId)
            ->where('is_active', true)
            ->where(function($query) use ($carbonDate) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $carbonDate->toDateString());
            })
            ->get()
            ->filter(function($class) use ($dayName, $carbonDate) {
                $schedule = $class->class_schedule ?? [];
                return isset($schedule[$dayName]);
            })
            ->map(function($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'coach' => $class->coach ? $class->coach->user->name : 'Atanmamış',
                    'time' => $this->getClassTimeForDay($class, strtolower(\Carbon\Carbon::parse(request()->input('date'))->format('l'))),
                ];
            })
            ->values();
        
        return response()->json(['classes' => $classes]);
    }

    /**
     * Belirli bir gün için ders saatini getir
     */
    private function getClassTimeForDay(ClassModel $class, $dayName)
    {
        $schedule = $class->class_schedule ?? [];
        if (isset($schedule[$dayName]) && is_array($schedule[$dayName])) {
            $time = $schedule[$dayName];
            return ($time['start_time'] ?? '') . ' - ' . ($time['end_time'] ?? '');
        }
        return '';
    }
}
