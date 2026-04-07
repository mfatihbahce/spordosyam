<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use App\Models\StudentFee;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $parent = Auth::user()->parent;

        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        $studentIds = $parent->students->pluck('id');

        $fees = StudentFee::whereIn('student_id', $studentIds)
            ->with(['student', 'payments'])
            ->orderBy('due_date', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'invoices' => $fees->map(function ($f) {
                $payment = $f->payments->where('status', 'completed')->sortByDesc('paid_at')->first();
                return [
                    'id' => $f->id,
                    'label' => $f->fee_label,
                    'amount' => (float) $f->amount,
                    'due_date' => $f->due_date?->format('Y-m-d'),
                    'status' => $f->status,
                    'student' => [
                        'id' => $f->student->id,
                        'name' => trim($f->student->first_name . ' ' . $f->student->last_name),
                    ],
                    'last_payment' => $payment ? [
                        'id' => $payment->id,
                        'amount' => (float) $payment->amount,
                        'status' => $payment->status,
                        'paid_at' => $payment->paid_at?->toIso8601String(),
                    ] : null,
                ];
            })->values(),
        ]);
    }
}

