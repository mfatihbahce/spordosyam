<?php

namespace App\Http\Controllers\Api\Coach;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $coach = $user->coach;

        if (!$coach) {
            return response()->json(['message' => 'Antrenör bulunamadı.'], 404);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'coach' => [
                'id' => $coach->id,
                'phone' => $coach->phone,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $coach = $user->coach;

        if (!$coach) {
            return response()->json(['message' => 'Antrenör bulunamadı.'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['message' => 'Mevcut şifre yanlış.'], 422);
            }
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        unset($validated['current_password']);
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            ...(isset($validated['password']) ? ['password' => $validated['password']] : []),
        ]);
        $coach->update(['phone' => $request->phone]);

        return response()->json([
            'message' => 'Profil başarıyla güncellendi.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }
}
