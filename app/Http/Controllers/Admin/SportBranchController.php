<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SportBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SportBranchController extends Controller
{
    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $sportBranches = SportBranch::where('school_id', $schoolId)
            ->withCount('classes')
            ->paginate(15);
        
        return view('admin.sport-branches.index', compact('sportBranches'));
    }

    public function create()
    {
        return view('admin.sport-branches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['school_id'] = Auth::user()->school_id;
        $validated['is_active'] = true;

        SportBranch::create($validated);

        return redirect()->route('admin.sport-branches.index')
            ->with('success', 'Branş başarıyla oluşturuldu.');
    }

    public function show(SportBranch $sportBranch)
    {
        if ($sportBranch->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        $sportBranch->loadCount('classes');
        return view('admin.sport-branches.show', compact('sportBranch'));
    }

    public function edit(SportBranch $sportBranch)
    {
        if ($sportBranch->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        return view('admin.sport-branches.edit', compact('sportBranch'));
    }

    public function update(Request $request, SportBranch $sportBranch)
    {
        if ($sportBranch->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $sportBranch->update($validated);

        return redirect()->route('admin.sport-branches.index')
            ->with('success', 'Branş başarıyla güncellendi.');
    }

    public function destroy(SportBranch $sportBranch)
    {
        if ($sportBranch->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        $sportBranch->delete();

        return redirect()->route('admin.sport-branches.index')
            ->with('success', 'Branş başarıyla silindi.');
    }
}
