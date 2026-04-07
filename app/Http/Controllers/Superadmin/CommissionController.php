<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index()
    {
        $schools = School::with('distributions')
            ->whereNotNull('iyzico_sub_merchant_key')
            ->paginate(15);
        
        return view('superadmin.commission.index', compact('schools'));
    }

    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'iyzico_commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        $school->update($validated);

        return redirect()->route('superadmin.commission.index')
            ->with('success', 'Komisyon oranı başarıyla güncellendi.');
    }
}
