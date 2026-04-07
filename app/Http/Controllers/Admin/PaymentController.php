<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $payments = Payment::where('school_id', $schoolId)
            ->with(['studentFee.student', 'parent.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $schoolId = Auth::user()->school_id;
        
        if ($payment->school_id !== $schoolId) {
            abort(403);
        }
        
        $payment->load(['studentFee.student', 'parent.user', 'school']);
        
        return view('admin.payments.show', compact('payment'));
    }
}
