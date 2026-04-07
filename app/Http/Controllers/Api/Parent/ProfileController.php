<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $parent = $user->parent;

        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'parent' => [
                'id' => $parent->id,
                'phone' => $parent->phone,
                'address' => $parent->address,
                'identity_number' => $parent->identity_number ? substr($parent->identity_number, 0, 3) . '*****' . substr($parent->identity_number, -2) : null,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $parent = $user->parent;

        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
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

        $parent->update([
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

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
