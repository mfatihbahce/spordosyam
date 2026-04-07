<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['studentFee.student', 'parent.user', 'school'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // İstatistikler
        $stats = [
            'total' => Payment::count(),
            'completed' => Payment::where('status', 'completed')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'total_amount' => Payment::where('status', 'completed')->sum('amount'),
        ];
        
        return view('superadmin.payments.index', compact('payments', 'stats'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['studentFee.student', 'parent.user', 'school']);
        return view('superadmin.payments.show', compact('payment'));
    }
}
