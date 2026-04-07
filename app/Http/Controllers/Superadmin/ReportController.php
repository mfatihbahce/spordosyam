<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Distribution;
use App\Models\LicenseExtension;
use App\Models\Payment;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $stats = [
            'total_schools' => School::count(),
            'active_license_schools' => School::activeLicense()->count(),
            'expired_license_schools' => School::expiredLicense()->count(),
            'total_payment_amount' => (float) Payment::where('status', 'completed')->sum('amount'),
            'total_payment_count' => Payment::where('status', 'completed')->count(),
            'monthly_payment_amount' => (float) Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'total_distribution_amount' => (float) Distribution::where('status', 'completed')->sum('net_amount'),
            'total_commission_earned' => (float) Distribution::where('status', 'completed')->sum('commission'),
            'total_users' => User::count(),
            'users_admin' => User::where('role', 'admin')->count(),
            'users_coach' => User::where('role', 'coach')->count(),
            'users_parent' => User::where('role', 'parent')->count(),
            'applications_pending' => Application::where('status', 'pending')->count(),
            'applications_approved' => Application::where('status', 'approved')->count(),
            'applications_rejected' => Application::where('status', 'rejected')->count(),
            'total_extension_revenue' => (float) LicenseExtension::sum('amount'),
        ];

        $recentPayments = Payment::where('status', 'completed')
            ->with('school')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentDistributions = Distribution::where('status', 'completed')
            ->with('school')
            ->orderBy('processed_at', 'desc')
            ->limit(10)
            ->get();

        $schoolSummary = School::withCount(['students', 'classes'])
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'license_type', 'demo_expires_at']);

        return view('superadmin.reports.index', compact('stats', 'recentPayments', 'recentDistributions', 'schoolSummary'));
    }
}
