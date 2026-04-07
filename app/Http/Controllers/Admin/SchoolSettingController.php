<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolSettingController extends Controller
{
    public function index()
    {
        $school = Auth::user()->school;
        return view('admin.school-settings.index', compact('school'));
    }

    public function update(Request $request)
    {
        $school = Auth::user()->school;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:schools,email,' . $school->id,
            'makeup_class_enabled' => 'boolean',
        ]);

        $school->update($validated);

        return redirect()->route('admin.school-settings.index')
            ->with('success', 'Okul bilgileri başarıyla güncellendi.');
    }
}
