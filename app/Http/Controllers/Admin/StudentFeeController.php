<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentFee;
use App\Models\Student;
use App\Models\FeePlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentFeeController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $query = StudentFee::whereHas('student', function($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->with(['student', 'feePlan', 'payments'])
            ->orderBy('due_date', 'desc');
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        $studentFees = $query->paginate(15)->withQueryString();
        return view('admin.student-fees.index', compact('studentFees'));
    }

    public function create()
    {
        $schoolId = Auth::user()->school_id;
        $students = Student::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();
        $feePlans = FeePlan::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();
        
        return view('admin.student-fees.create', compact('students', 'feePlans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_plan_id' => 'required|exists:fee_plans,id',
            'day_of_month' => 'required|integer|min:1|max:31',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $schoolId = Auth::user()->school_id;

        $student = Student::findOrFail($validated['student_id']);
        if ($student->school_id !== $schoolId) {
            abort(403);
        }
        if (!$student->is_active) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Sadece aktif öğrencilere aidat tanımlanabilir. Öğrenci pasif.');
        }

        $feePlan = FeePlan::findOrFail($validated['fee_plan_id']);
        if ($feePlan->school_id !== $schoolId) {
            abort(403);
        }

        $amount = (isset($validated['amount']) && $validated['amount'] > 0)
            ? (float) $validated['amount']
            : (float) $feePlan->amount;

        $dayOfMonth = (int) $validated['day_of_month'];
        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);

        $dueDates = [];
        $current = $start->copy()->startOfMonth();
        while ($current->lte($end)) {
            $lastDay = $current->daysInMonth;
            $day = min($dayOfMonth, $lastDay);
            $dueDate = $current->copy()->day($day);
            if ($dueDate->gte($start) && $dueDate->lte($end)) {
                $dueDates[] = $dueDate->format('Y-m-d');
            }
            $current->addMonth();
        }

        if (empty($dueDates)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Başlangıç ve bitiş tarihi arasında oluşacak vade bulunamadı. Ayın ' . $dayOfMonth . '. günü bu aralıkta yok.');
        }

        foreach ($dueDates as $dueDate) {
            StudentFee::create([
                'student_id' => $student->id,
                'fee_plan_id' => $feePlan->id,
                'amount' => $amount,
                'due_date' => $dueDate,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);
        }

        $count = count($dueDates);
        return redirect()->route('admin.student-fees.index')
            ->with('success', "Öğrenci için {$count} adet aylık aidat oluşturuldu (her ayın {$dayOfMonth}. günü).");
    }

    public function show(StudentFee $studentFee)
    {
        $schoolId = Auth::user()->school_id;
        
        if ($studentFee->student->school_id !== $schoolId) {
            abort(403);
        }
        
        $studentFee->load(['student', 'feePlan', 'payments.parent.user']);
        
        return view('admin.student-fees.show', compact('studentFee'));
    }

    public function edit(StudentFee $studentFee)
    {
        $schoolId = Auth::user()->school_id;
        
        if ($studentFee->student->school_id !== $schoolId) {
            abort(403);
        }
        
        $students = Student::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();
        $feePlans = FeePlan::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();
        
        return view('admin.student-fees.edit', compact('studentFee', 'students', 'feePlans'));
    }

    public function update(Request $request, StudentFee $studentFee)
    {
        $schoolId = Auth::user()->school_id;
        
        if ($studentFee->student->school_id !== $schoolId) {
            abort(403);
        }
        
        $rules = [
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,paid,overdue,cancelled',
            'notes' => 'nullable|string',
        ];
        if ($studentFee->fee_plan_id !== null) {
            $rules['fee_plan_id'] = 'required|exists:fee_plans,id';
        }
        $validated = $request->validate($rules);

        $student = Student::findOrFail($validated['student_id']);
        if ($student->school_id !== $schoolId) {
            abort(403);
        }
        if (isset($validated['fee_plan_id'])) {
            $feePlan = FeePlan::findOrFail($validated['fee_plan_id']);
            if ($feePlan->school_id !== $schoolId) {
                abort(403);
            }
        } else {
            unset($validated['fee_plan_id']);
        }

        $studentFee->update($validated);

        return redirect()->route('admin.student-fees.index')
            ->with('success', 'Öğrenci aidatı başarıyla güncellendi.');
    }

    public function destroy(StudentFee $studentFee)
    {
        $schoolId = Auth::user()->school_id;
        
        if ($studentFee->student->school_id !== $schoolId) {
            abort(403);
        }
        
        $studentFee->delete();

        return redirect()->route('admin.student-fees.index')
            ->with('success', 'Öğrenci aidatı başarıyla silindi.');
    }
}
