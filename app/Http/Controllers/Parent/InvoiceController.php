<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\StudentFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $parent = Auth::user()->parent;
        
        if (!$parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Veli bilgileriniz bulunamadı.');
        }

        $studentIds = $parent->students->pluck('id');
        
        $invoices = StudentFee::whereIn('student_id', $studentIds)
            ->with(['student', 'feePlan', 'payments'])
            ->orderBy('due_date', 'desc')
            ->paginate(20);

        return view('parent.invoices.index', compact('invoices'));
    }
}
