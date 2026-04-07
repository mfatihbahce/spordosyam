<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\FeePlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeePlanController extends Controller
{
    /**
     * Okulun en az bir aktif banka hesabı var mı kontrol et.
     */
    private function schoolHasActiveBankAccount(): bool
    {
        $schoolId = Auth::user()->school_id;
        return BankAccount::where('school_id', $schoolId)->where('is_active', true)->exists();
    }

    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $feePlans = FeePlan::where('school_id', $schoolId)
            ->withCount('studentFees')
            ->paginate(15);
        
        return view('admin.fee-plans.index', compact('feePlans'));
    }

    public function create()
    {
        if (!$this->schoolHasActiveBankAccount()) {
            return redirect()->route('admin.bank-accounts.index')
                ->with('warning', 'Aidat Planları eklemek için IBAN yönetimi sayfasından banka hesabı eklemeniz ve hesabın aktif olması gerekmektedir.');
        }

        return view('admin.fee-plans.create');
    }

    public function store(Request $request)
    {
        if (!$this->schoolHasActiveBankAccount()) {
            return redirect()->route('admin.bank-accounts.index')
                ->with('warning', 'Aidat Planları eklemek için IBAN yönetimi sayfasından banka hesabı eklemeniz ve hesabın aktif olması gerekmektedir.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $validated['school_id'] = Auth::user()->school_id;
        $validated['is_active'] = true;

        FeePlan::create($validated);

        return redirect()->route('admin.fee-plans.index')
            ->with('success', 'Aidat planı başarıyla oluşturuldu.');
    }

    public function show(FeePlan $feePlan)
    {
        if ($feePlan->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        $feePlan->loadCount('studentFees');
        $feePlan->load(['studentFees.student']);
        
        return view('admin.fee-plans.show', compact('feePlan'));
    }

    public function edit(FeePlan $feePlan)
    {
        if ($feePlan->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        return view('admin.fee-plans.edit', compact('feePlan'));
    }

    public function update(Request $request, FeePlan $feePlan)
    {
        if ($feePlan->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $feePlan->update($validated);

        return redirect()->route('admin.fee-plans.index')
            ->with('success', 'Aidat planı başarıyla güncellendi.');
    }

    public function destroy(FeePlan $feePlan)
    {
        if ($feePlan->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        $feePlan->delete();

        return redirect()->route('admin.fee-plans.index')
            ->with('success', 'Aidat planı başarıyla silindi.');
    }
}
