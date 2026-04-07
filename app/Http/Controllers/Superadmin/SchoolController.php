<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::activeLicense()
            ->withCount(['students', 'coaches', 'classes'])
            ->orderBy('demo_expires_at', 'asc') // önce bitişe yakın olanlar
            ->paginate(15);
        return view('superadmin.schools.index', compact('schools'));
    }

    /** Lisans süresi dolmuş okullar listesi */
    public function expired()
    {
        $schools = School::expiredLicense()
            ->withCount(['students', 'coaches', 'classes'])
            ->orderBy('demo_expires_at', 'desc')
            ->paginate(15);
        return view('superadmin.schools.expired', compact('schools'));
    }

    public function create()
    {
        return view('superadmin.schools.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:schools,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = true;
        $validated['iyzico_commission_rate'] = (float) (env('DEFAULT_COMMISSION_RATE', 5));

        School::create($validated);

        return redirect()->route('superadmin.schools.index')
            ->with('success', 'Spor okulu başarıyla oluşturuldu.');
    }

    public function show(School $school)
    {
        $school->loadCount(['students', 'coaches', 'classes', 'branches', 'sportBranches']);
        $school->load(['licenseExtensions' => fn ($q) => $q->orderBy('extended_at', 'desc')]);
        return view('superadmin.schools.show', compact('school'));
    }

    /** Lisans uzatımı uygula (modal veya sayfa formundan) */
    public function extendLicense(Request $request, School $school)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
            'amount' => 'nullable|numeric|min:0',
        ], [
            'days.required' => 'Ek süre (gün) girin.',
            'days.min' => 'En az 1 gün girin.',
            'days.max' => 'En fazla 365 gün girin.',
        ]);

        $days = (int) $request->days;
        $amount = $request->filled('amount') ? (float) $request->amount : null;
        $currentExpiry = $school->demo_expires_at;

        if (!$currentExpiry || $currentExpiry->endOfDay()->isPast()) {
            $newExpiry = Carbon::now()->addDays($days);
        } else {
            $newExpiry = $currentExpiry->copy()->addDays($days);
        }

        $school->update([
            'demo_expires_at' => $newExpiry,
            'license_extended_count' => ($school->license_extended_count ?? 0) + 1,
        ]);

        \App\Models\LicenseExtension::create([
            'school_id' => $school->id,
            'extended_at' => Carbon::now(),
            'days_added' => $days,
            'amount' => $amount,
            'extended_by' => auth()->id(),
        ]);

        $msg = "Lisans {$days} gün uzatıldı. Yeni bitiş tarihi: " . $newExpiry->format('d.m.Y');
        if ($amount !== null && $amount > 0) {
            $msg .= '. Alınan ücret: ' . number_format($amount, 2, ',', '.') . ' ₺';
        }
        $msg .= '.';

        return redirect()->route('superadmin.schools.show', $school)->with('success', $msg);
    }

    public function edit(School $school)
    {
        return view('superadmin.schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:schools,email,' . $school->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $school->update($validated);

        return redirect()->route('superadmin.schools.index')
            ->with('success', 'Spor okulu başarıyla güncellendi.');
    }

    public function destroy(School $school)
    {
        $school->delete();
        return redirect()->route('superadmin.schools.index')
            ->with('success', 'Spor okulu başarıyla silindi.');
    }
}
