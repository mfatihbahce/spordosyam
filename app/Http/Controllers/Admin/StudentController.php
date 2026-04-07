<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\ClassModel;
use App\Models\StudentClassHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $query = Student::where('school_id', $schoolId)->with(['classModel', 'parents.user']);

        if ($request->filled('tc')) {
            $query->where('identity_number', $request->input('tc'));
        }
        if ($request->filled('parent_name')) {
            $name = $request->input('parent_name');
            $query->whereHas('parents.user', function ($q) use ($name) {
                $q->where('name', 'like', '%' . $name . '%');
            });
        }

        $students = $query->orderBy('first_name')->paginate(15)->withQueryString();
        return view('admin.students.index', compact('students'));
    }

    /**
     * TC kimlik no ile öğrenci bul (veli formunda kullanım için)
     */
    public function findByTc(Request $request)
    {
        $tc = $request->input('tc');
        if (!$tc || !preg_match('/^[0-9]{11}$/', $tc)) {
            return response()->json(['found' => false, 'message' => 'Geçerli 11 haneli TC girin.'], 422);
        }
        $schoolId = Auth::user()->school_id;
        $student = Student::where('school_id', $schoolId)
            ->where('identity_number', $tc)
            ->first();
        if (!$student) {
            return response()->json(['found' => false, 'message' => 'Bu TC kimlik numarasına kayıtlı öğrenci bulunamadı.'], 404);
        }
        return response()->json([
            'found' => true,
            'id' => $student->id,
            'name' => trim($student->first_name . ' ' . $student->last_name),
        ]);
    }

    public function create(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $preselectedClassId = $request->query('class_id');
        $classes = ClassModel::where('school_id', $schoolId)
            ->where(function ($q) use ($preselectedClassId) {
                $q->where('is_active', true);
                if ($preselectedClassId) {
                    $q->orWhere('id', $preselectedClassId);
                }
            })
            ->get();

        return view('admin.students.create', compact('classes', 'preselectedClassId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'identity_number' => 'nullable|string|size:11|regex:/^[0-9]+$/',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'class_id' => 'nullable|exists:classes,id',
            'notes' => 'nullable|string',
        ]);

        $validated['school_id'] = Auth::user()->school_id;
        $validated['is_active'] = true;

        $student = Student::create($validated);
        if (!empty($validated['class_id'])) {
            $class = ClassModel::find($validated['class_id']);
            $credits = $class ? (int) ($class->default_credits ?? 8) : 8;
            StudentClassHistory::create([
                'student_id' => $student->id,
                'class_id' => $validated['class_id'],
                'enrolled_at' => $student->created_at,
                'left_at' => null,
                'total_credits' => $credits,
                'used_credits' => 0,
            ]);
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Öğrenci başarıyla oluşturuldu.');
    }

    public function show(Student $student)
    {
        if ($student->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        $student->load([
            'classModel.sportBranch',
            'classModel.branch',
            'classModel.coach.user',
            'parents.user',
            'attendances',
            'studentFees',
            'classHistory' => fn ($q) => $q->with(['classModel.sportBranch', 'classModel.branch']),
        ]);
        
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        if ($student->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        $schoolId = Auth::user()->school_id;
        $classes = ClassModel::where('school_id', $schoolId)
            ->where(function ($q) use ($student) {
                $q->where('is_active', true);
                if ($student->class_id) {
                    $q->orWhere('id', $student->class_id);
                }
            })
            ->get();
        
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        if ($student->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'identity_number' => 'nullable|string|size:11|regex:/^[0-9]+$/',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'class_id' => 'nullable|exists:classes,id',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $oldClassId = $student->class_id;
        $newClassId = $validated['class_id'] ?? null;

        $student->update($validated);

        if ($oldClassId != $newClassId) {
            StudentClassHistory::where('student_id', $student->id)->whereNull('left_at')
                ->update(['left_at' => now(), 'leave_reason' => 'transferred']);
            if ($newClassId) {
                $class = ClassModel::find($newClassId);
                $credits = $class ? (int) ($class->default_credits ?? 8) : 8;
                StudentClassHistory::create([
                    'student_id' => $student->id,
                    'class_id' => $newClassId,
                    'enrolled_at' => now(),
                    'left_at' => null,
                    'total_credits' => $credits,
                    'used_credits' => 0,
                ]);
            }
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Öğrenci başarıyla güncellendi.');
    }

    public function destroy(Student $student)
    {
        if ($student->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Öğrenci başarıyla silindi.');
    }

    /**
     * Öğrenciye aidat tanımla (tutar + vade). Plan kullanılmaz, doğrudan aidat kaydı oluşturulur.
     */
    public function storeAidat(Request $request, Student $student)
    {
        if ($student->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        StudentFee::create([
            'student_id' => $student->id,
            'fee_plan_id' => null,
            'amount' => (float) $validated['amount'],
            'due_date' => $validated['due_date'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Aidat başarıyla tanımlandı. Veli panelden tutarı görüp ödeyebilir.');
    }
}
