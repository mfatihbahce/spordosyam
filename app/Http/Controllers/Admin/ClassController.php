<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\SportBranch;
use App\Models\Branch;
use App\Models\Coach;
use App\Models\Student;
use App\Models\StudentClassHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $classes = ClassModel::where('school_id', $schoolId)
            ->with(['sportBranch', 'coach.user', 'branch'])
            ->withCount(['currentEnrollments', 'pastEnrollments'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Bitiş tarihi geçen sınıfları kontrol et: mezun say, sınıfı kapat
        foreach ($classes as $class) {
            if ($class->end_date && $class->end_date < now()->toDateString() && $class->is_active) {
                $leftAt = $class->end_date->format('Y-m-d') . ' 23:59:59';
                StudentClassHistory::where('class_id', $class->id)->whereNull('left_at')
                    ->update(['left_at' => $leftAt, 'leave_reason' => 'graduated']);
                Student::where('class_id', $class->id)->update(['class_id' => null]);
                $class->update(['is_active' => false]);
            }
        }
        
        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        $schoolId = Auth::user()->school_id;
        $sportBranches = SportBranch::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();
        $branches = Branch::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();
        $coaches = Coach::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('user')
            ->get();
        
        return view('admin.classes.create', compact('sportBranches', 'branches', 'coaches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sport_branch_id' => 'required|exists:sport_branches,id',
            'branch_id' => 'nullable|exists:branches,id',
            'coach_id' => 'nullable|exists:coaches,id',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'class_days' => 'nullable|array',
            'class_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'class_schedule' => 'nullable|array',
            'class_schedule.*.start_time' => 'required_with:class_schedule.*|date_format:H:i',
            'class_schedule.*.end_time' => 'required_with:class_schedule.*|date_format:H:i|after:class_schedule.*.start_time',
            'end_date' => 'nullable|date|after_or_equal:today',
            'default_credits' => 'nullable|integer|min:1',
        ]);

        $validated['school_id'] = Auth::user()->school_id;
        $validated['is_active'] = true;
        $validated['capacity'] = $validated['capacity'] ?? 20;
        $validated['default_credits'] = $validated['default_credits'] ?? 8;

        // class_schedule'i sadece seçilen günler için oluştur (başlangıç ve bitiş saati)
        if ($request->has('class_days') && $request->has('class_schedule')) {
            $schedule = [];
            foreach ($request->class_days as $day) {
                if (!empty($request->class_schedule[$day]['start_time']) && !empty($request->class_schedule[$day]['end_time'])) {
                    $schedule[$day] = [
                        'start_time' => $request->class_schedule[$day]['start_time'],
                        'end_time' => $request->class_schedule[$day]['end_time'],
                    ];
                }
            }
            $validated['class_schedule'] = !empty($schedule) ? $schedule : null;
        } else {
            $validated['class_schedule'] = null;
        }

        ClassModel::create($validated);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Sınıf başarıyla oluşturuldu.');
    }

    /**
     * AJAX: Bu sınıfa eklenebilecek öğrenciler (bu sınıfta olmayanlar), arama ile
     */
    public function studentsToAdd(Request $request, ClassModel $class)
    {
        if ($class->school_id !== Auth::user()->school_id) {
            return response()->json(['error' => 'Yetkisiz'], 403);
        }
        $search = $request->input('search', '');
        $query = Student::where('school_id', $class->school_id)
            ->where(function ($q) use ($class) {
                $q->whereNull('class_id')->orWhere('class_id', '!=', $class->id);
            });
        if ($search !== '') {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('identity_number', 'like', $term);
            });
        }
        $students = $query->orderBy('first_name')->orderBy('last_name')->limit(100)->get(['id', 'first_name', 'last_name', 'phone', 'email', 'class_id']);
        $list = $students->map(function ($s) {
            return [
                'id' => $s->id,
                'name' => trim($s->first_name . ' ' . $s->last_name),
                'phone' => $s->phone ?? '-',
                'email' => $s->email ?? '-',
            ];
        });
        return response()->json(['students' => $list]);
    }

    /**
     * Seçilen öğrencileri bu sınıfa ata
     */
    public function assignStudents(Request $request, ClassModel $class)
    {
        if ($class->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'integer|exists:students,id',
        ]);
        $studentIds = array_unique($validated['student_ids']);
        $schoolId = Auth::user()->school_id;
        $currentCount = StudentClassHistory::where('class_id', $class->id)->whereNull('left_at')->count();
        $capacityLeft = $class->capacity - $currentCount;
        $toAdd = min(count($studentIds), max(0, $capacityLeft));
        $added = 0;
        foreach ($studentIds as $studentId) {
            if ($added >= $toAdd) {
                break;
            }
            $student = Student::where('id', $studentId)->where('school_id', $schoolId)->first();
            if (!$student) {
                continue;
            }
            if (StudentClassHistory::where('class_id', $class->id)->where('student_id', $student->id)->whereNull('left_at')->exists()) {
                continue;
            }
            $student->update(['class_id' => $class->id]);
            $credits = (int) ($class->default_credits ?? 8);
            StudentClassHistory::create([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'enrolled_at' => now(),
                'left_at' => null,
                'total_credits' => $credits,
                'used_credits' => 0,
            ]);
            $added++;
        }
        return redirect()->back()->with('success', $added . ' öğrenci bu sınıfa eklendi.');
    }

    public function show(ClassModel $class)
    {
        if ($class->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        $class->load(['sportBranch', 'coach.user', 'branch']);
        $currentEnrollments = StudentClassHistory::where('class_id', $class->id)
            ->whereNull('left_at')
            ->with('student')
            ->orderBy('enrolled_at')
            ->get();
        $pastEnrollments = StudentClassHistory::where('class_id', $class->id)
            ->whereNotNull('left_at')
            ->with('student')
            ->orderByDesc('left_at')
            ->get();
        $isClassClosed = !$class->is_active || ($class->end_date && $class->end_date->lt(now()->toDateString()));
        return view('admin.classes.show', compact('class', 'currentEnrollments', 'pastEnrollments', 'isClassClosed'));
    }

    public function edit(ClassModel $class)
    {
        if ($class->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        $schoolId = Auth::user()->school_id;
        $sportBranches = SportBranch::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();
        $branches = Branch::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();
        $coaches = Coach::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('user')
            ->get();
        
        return view('admin.classes.edit', compact('class', 'sportBranches', 'branches', 'coaches'));
    }

    public function update(Request $request, ClassModel $class)
    {
        if ($class->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sport_branch_id' => 'required|exists:sport_branches,id',
            'branch_id' => 'nullable|exists:branches,id',
            'coach_id' => 'nullable|exists:coaches,id',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'class_days' => 'nullable|array',
            'class_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'class_schedule' => 'nullable|array',
            'class_schedule.*.start_time' => 'required_with:class_schedule.*|date_format:H:i',
            'class_schedule.*.end_time' => 'required_with:class_schedule.*|date_format:H:i|after:class_schedule.*.start_time',
            'end_date' => 'nullable|date',
            'is_active' => 'boolean',
            'default_credits' => 'nullable|integer|min:1',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        if (!isset($validated['default_credits'])) {
            $validated['default_credits'] = $class->default_credits ?? 8;
        }

        // class_schedule'i sadece seçilen günler için oluştur (başlangıç ve bitiş saati)
        if ($request->has('class_days') && $request->has('class_schedule')) {
            $schedule = [];
            foreach ($request->class_days as $day) {
                if (!empty($request->class_schedule[$day]['start_time']) && !empty($request->class_schedule[$day]['end_time'])) {
                    $schedule[$day] = [
                        'start_time' => $request->class_schedule[$day]['start_time'],
                        'end_time' => $request->class_schedule[$day]['end_time'],
                    ];
                }
            }
            $validated['class_schedule'] = !empty($schedule) ? $schedule : null;
        } else {
            $validated['class_schedule'] = null;
        }

        $class->update($validated);

        // Sınıf pasif veya bitiş tarihi geçmişse bu sınıftaki tüm öğrencileri pasif yap
        $classActuallyInactive = !$validated['is_active'] || ($class->end_date && $class->end_date->lt(now()));
        if ($classActuallyInactive) {
            $class->students()->update(['is_active' => false]);
        }

        return redirect()->route('admin.classes.index')
            ->with('success', 'Sınıf başarıyla güncellendi.');
    }

    public function destroy(ClassModel $class)
    {
        if ($class->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Sınıf başarıyla silindi.');
    }
}
