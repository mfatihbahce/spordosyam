<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Student;
use App\Models\Coach;
use App\Models\Payment;
use App\Models\StudentFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $branches = Branch::where('school_id', $schoolId)->paginate(15);
        
        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);

        $validated['school_id'] = Auth::user()->school_id;
        $validated['is_active'] = true;

        Branch::create($validated);

        return redirect()->route('admin.branches.index')
            ->with('success', 'Şube başarıyla oluşturuldu.');
    }

    public function show(Branch $branch)
    {
        if ($branch->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        $branch->loadCount('classes');

        $studentsCount = Student::whereHas('classModel', fn ($q) => $q->where('branch_id', $branch->id))
            ->count();
        $coachesCount = Coach::whereHas('classes', fn ($q) => $q->where('branch_id', $branch->id))
            ->count();
        $totalDuesCollected = Payment::where('status', 'completed')
            ->whereHas('studentFee.student.classModel', fn ($q) => $q->where('branch_id', $branch->id))
            ->sum(DB::raw('amount'));
        $pendingDuesAmount = StudentFee::whereIn('status', ['pending', 'overdue'])
            ->whereHas('student.classModel', fn ($q) => $q->where('branch_id', $branch->id))
            ->sum(DB::raw('amount'));
        $pendingDuesCount = StudentFee::whereIn('status', ['pending', 'overdue'])
            ->whereHas('student.classModel', fn ($q) => $q->where('branch_id', $branch->id))
            ->count();

        $branch->load(['classes' => fn ($q) => $q->withCount('students')->with('coach.user', 'sportBranch')]);

        return view('admin.branches.show', array_merge(compact('branch'), [
            'studentsCount' => $studentsCount,
            'coachesCount' => $coachesCount,
            'totalDuesCollected' => (float) $totalDuesCollected,
            'pendingDuesAmount' => (float) $pendingDuesAmount,
            'pendingDuesCount' => $pendingDuesCount,
        ]));
    }

    public function edit(Branch $branch)
    {
        if ($branch->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        if ($branch->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $branch->update($validated);

        return redirect()->route('admin.branches.index')
            ->with('success', 'Şube başarıyla güncellendi.');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        $branch->delete();

        return redirect()->route('admin.branches.index')
            ->with('success', 'Şube başarıyla silindi.');
    }
}
