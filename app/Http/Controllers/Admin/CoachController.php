<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CoachController extends Controller
{
    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $coaches = Coach::where('school_id', $schoolId)
            ->with('user')
            ->paginate(15);
        
        return view('admin.coaches.index', compact('coaches'));
    }

    public function create()
    {
        return view('admin.coaches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
        ]);

        $schoolId = Auth::user()->school_id;

        // Kullanıcı oluştur
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'coach',
            'school_id' => $schoolId,
            'is_active' => true,
        ]);

        // Antrenör oluştur
        Coach::create([
            'school_id' => $schoolId,
            'user_id' => $user->id,
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('admin.coaches.index')
            ->with('success', 'Antrenör başarıyla oluşturuldu.');
    }

    public function show(Coach $coach)
    {
        if ($coach->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        $coach->load(['user', 'classes', 'attendances']);
        
        return view('admin.coaches.show', compact('coach'));
    }

    public function edit(Coach $coach)
    {
        if ($coach->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        return view('admin.coaches.edit', compact('coach'));
    }

    public function update(Request $request, Coach $coach)
    {
        if ($coach->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $coach->user_id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Kullanıcıyı güncelle
        $coach->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Antrenörü güncelle
        $coach->update([
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.coaches.index')
            ->with('success', 'Antrenör başarıyla güncellendi.');
    }

    public function destroy(Coach $coach)
    {
        if ($coach->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        $coach->user->delete(); // User silindiğinde coach de cascade ile silinir
        $coach->delete();

        return redirect()->route('admin.coaches.index')
            ->with('success', 'Antrenör başarıyla silindi.');
    }
}
