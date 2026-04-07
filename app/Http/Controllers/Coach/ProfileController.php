<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $coach = $user->coach;
        return view('coach.profile.index', compact('user', 'coach'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
        ]);

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Mevcut şifre yanlış.']);
            }
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        unset($validated['current_password']);
        $user->update($validated);

        if ($user->coach) {
            $user->coach->update([
                'phone' => $request->phone,
                'bio' => $request->bio,
            ]);
        }

        return redirect()->route('coach.profile.index')
            ->with('success', 'Profil başarıyla güncellendi.');
    }
}
