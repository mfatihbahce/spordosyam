<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Distribution;
use App\Models\School;
use Illuminate\Http\Request;

class DistributionController extends Controller
{
    public function index()
    {
        $distributions = Distribution::with(['school', 'bankAccount'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // İstatistikler
        $stats = [
            'total' => Distribution::count(),
            'completed' => Distribution::where('status', 'completed')->count(),
            'pending' => Distribution::where('status', 'pending')->count(),
            'total_amount' => Distribution::where('status', 'completed')->sum('net_amount'),
            'total_commission' => Distribution::where('status', 'completed')->sum('commission'),
        ];
        
        return view('superadmin.distributions.index', compact('distributions', 'stats'));
    }

    public function show(Distribution $distribution)
    {
        $distribution->load(['school', 'bankAccount']);
        return view('superadmin.distributions.show', compact('distribution'));
    }
}
