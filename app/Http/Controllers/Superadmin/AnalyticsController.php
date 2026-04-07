<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Distribution;
use App\Models\Payment;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Son 12 ayın ödeme verileri
        $monthlyPayments = Payment::where('status', 'completed')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('YEAR(created_at) as year'), DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Okul bazında ödeme dağılımı
        $schoolPayments = Payment::where('status', 'completed')
            ->select('school_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('school_id')
            ->with('school')
            ->orderBy('total', 'desc')
            ->get();

        // Günlük ödeme trendi (son 30 gün)
        $dailyPayments = Payment::where('status', 'completed')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Toplam istatistikler
        $stats = [
            'total_revenue' => (float) Payment::where('status', 'completed')->sum('amount'),
            'monthly_revenue' => (float) Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'total_transactions' => Payment::where('status', 'completed')->count(),
            'average_transaction' => (float) (Payment::where('status', 'completed')->avg('amount') ?? 0),
            'total_commission' => (float) Distribution::where('status', 'completed')->sum('commission'),
            'total_distributed' => (float) Distribution::where('status', 'completed')->sum('net_amount'),
        ];

        return view('superadmin.analytics.index', compact('monthlyPayments', 'schoolPayments', 'dailyPayments', 'stats'));
    }
}
