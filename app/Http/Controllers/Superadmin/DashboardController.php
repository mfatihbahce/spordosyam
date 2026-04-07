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

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_schools' => School::count(),
            'active_schools' => School::where('is_active', true)->count(),
            'active_license_schools' => School::activeLicense()->count(),
            'expired_license_schools' => School::expiredLicense()->count(),
            'pending_applications' => Application::where('status', 'pending')->count(),
            'applications_approved' => Application::where('status', 'approved')->count(),
            'total_payments' => (float) Payment::where('status', 'completed')->sum('amount'),
            'payment_count' => Payment::where('status', 'completed')->count(),
            'monthly_payments' => (float) Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'monthly_payment_count' => Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'total_users' => User::where('role', '!=', 'superadmin')->count(),
            'users_admin' => User::where('role', 'admin')->count(),
            'users_coach' => User::where('role', 'coach')->count(),
            'users_parent' => User::where('role', 'parent')->count(),
            'total_distributions' => (float) Distribution::where('status', 'completed')->sum('net_amount'),
            'total_commission' => (float) Distribution::where('status', 'completed')->sum('commission'),
            'total_extension_revenue' => (float) LicenseExtension::sum('amount'),
        ];

        $recent_applications = Application::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recent_payments = Payment::where('status', 'completed')
            ->with(['studentFee.student', 'school'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $recent_schools = School::orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'email', 'is_active', 'license_type', 'demo_expires_at', 'created_at']);

        $monthly_stats = Payment::where('status', 'completed')
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(amount) as total, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('superadmin.dashboard', compact('stats', 'recent_applications', 'recent_payments', 'recent_schools', 'monthly_stats'));
    }
}
