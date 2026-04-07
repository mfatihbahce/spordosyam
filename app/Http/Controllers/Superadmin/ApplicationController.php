<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\School;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('superadmin.applications.index', compact('applications'));
    }

    public function show(Application $application)
    {
        $defaultDemoDays = (int) SiteSetting::get('default_demo_days', 14);
        $school = null;
        if ($application->status === 'approved') {
            $school = School::where('email', $application->email)->first();
        }
        return view('superadmin.applications.show', compact('application', 'defaultDemoDays', 'school'));
    }

    public function approve(Request $request, Application $application)
    {
        $request->validate([
            'license_type' => 'required|in:demo,free,paid',
            'license_days' => 'required_unless:license_type,demo|nullable|integer|min:1|max:365',
            'paid_amount' => 'required_if:license_type,paid|nullable|numeric|min:0',
        ], [
            'license_type.required' => 'Lisans türü seçin.',
            'license_type.in' => 'Geçerli bir lisans türü seçin.',
            'license_days.required_unless' => 'Ücretsiz/ücretli lisans için süre (gün) girin.',
            'paid_amount.required_if' => 'Ücretli lisans için tutar girin.',
        ]);

        $licenseType = $request->license_type;
        // Demo: varsayılan gün ayarından (Genel Ayarlar). Ücretsiz/Ücretli: formdan.
        $licenseDays = $licenseType === 'demo'
            ? (int) SiteSetting::get('default_demo_days', 14)
            : (int) $request->license_days;
        $licenseDays = max(1, min(365, $licenseDays));

        $expiresAt = Carbon::now()->addDays($licenseDays);
        $paidAmount = $licenseType === 'paid' ? $request->paid_amount : null;

        $defaultCommissionRate = (float) (env('DEFAULT_COMMISSION_RATE', 5));
        $school = School::create([
            'name' => $application->school_name,
            'slug' => Str::slug($application->school_name),
            'email' => $application->email,
            'phone' => $application->phone,
            'address' => $application->address,
            'description' => $application->message,
            'is_active' => true,
            'is_demo' => in_array($licenseType, ['demo', 'free', 'paid'], true),
            'demo_expires_at' => $expiresAt,
            'license_type' => $licenseType,
            'paid_amount' => $paidAmount,
            'iyzico_commission_rate' => $defaultCommissionRate,
        ]);

        $adminPassword = $application->password
            ? $application->password
            : Hash::make(Str::random(12));

        $adminUser = User::create([
            'name' => $application->contact_name,
            'email' => $application->email,
            'password' => $adminPassword,
            'role' => 'admin',
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        $application->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'demo_days' => $licenseDays,
            'demo_expires_at' => $expiresAt,
            'license_type' => $licenseType,
            'paid_amount' => $paidAmount,
        ]);

        return redirect()->route('superadmin.applications.index')
            ->with('success', 'Başvuru onaylandı ve okul oluşturuldu.');
    }

    public function reject(Application $application)
    {
        $application->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('superadmin.applications.index')
            ->with('success', 'Başvuru reddedildi.');
    }
}
