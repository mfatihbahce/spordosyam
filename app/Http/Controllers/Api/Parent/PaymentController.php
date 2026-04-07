<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StudentFee;
use App\Services\IyzicoService;
use App\Services\SmsNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected IyzicoService $iyzicoService;

    public function __construct(IyzicoService $iyzicoService)
    {
        $this->iyzicoService = $iyzicoService;
    }

    /**
     * Ödenmemiş aidatlar listesi (parent.payments.index eşdeğeri).
     */
    public function pendingFees()
    {
        $parent = Auth::user()->parent;

        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        $studentIds = $parent->students->pluck('id');
        if ($studentIds->isEmpty()) {
            return response()->json(['fees' => []]);
        }

        $fees = StudentFee::whereIn('student_id', $studentIds)
            ->whereHas('student', fn($q) => $q->where('is_active', true))
            ->where('status', '!=', 'paid')
            ->with('student')
            ->orderBy('due_date', 'asc')
            ->get();

        return response()->json([
            'fees' => $fees->map(function ($f) {
                return [
                    'id' => $f->id,
                    'student' => [
                        'id' => $f->student->id,
                        'name' => trim($f->student->first_name . ' ' . $f->student->last_name),
                    ],
                    'label' => $f->fee_label,
                    'amount' => (float) $f->amount,
                    'due_date' => $f->due_date?->format('Y-m-d'),
                    'status' => $f->status,
                ];
            })->values(),
        ]);
    }

    /**
     * Ödeme geçmişi (tamamlanmış ödemeler).
     */
    public function history()
    {
        $parent = Auth::user()->parent;

        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        $payments = Payment::where('parent_id', $parent->id)
            ->with(['studentFee.student'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'payments' => $payments->map(function ($p) {
                $fee = $p->studentFee;
                $student = $fee?->student;
                return [
                    'id' => $p->id,
                    'amount' => (float) $p->amount,
                    'status' => $p->status,
                    'paid_at' => $p->paid_at?->toIso8601String(),
                    'student' => $student ? [
                        'id' => $student->id,
                        'name' => trim($student->first_name . ' ' . $student->last_name),
                    ] : null,
                    'fee' => $fee ? [
                        'id' => $fee->id,
                        'label' => $fee->fee_label,
                        'due_date' => $fee->due_date?->format('Y-m-d'),
                    ] : null,
                ];
            })->values(),
        ]);
    }

    /**
     * Mobilden ödeme alma (parent.payments.store mantığının JSON versiyonu).
     */
    public function pay(Request $request, StudentFee $studentFee)
    {
        $parent = Auth::user()->parent;
        $user = Auth::user();

        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        if (!$parent->students->contains($studentFee->student_id)) {
            return response()->json(['message' => 'Bu aidat size ait değil.'], 403);
        }

        $validated = $request->validate([
            'card_holder_name' => 'required|string|max:255',
            'card_number' => 'required|string|min:16|max:19',
            'expire_month' => 'required|string|size:2',
            'expire_year' => 'required|string|size:4',
            'cvc' => 'required|string|min:3|max:4',
        ]);

        if (empty($parent->identity_number)) {
            return response()->json([
                'message' => 'Ödeme için profilinizde TC Kimlik No gerekli.',
                'redirect_to' => route('parent.profile.index'),
            ], 422);
        }
        if (empty(trim($parent->address ?? ''))) {
            return response()->json([
                'message' => 'Ödeme için profilinizde adres bilgisi gerekli.',
                'redirect_to' => route('parent.profile.index'),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $payment = Payment::create([
                'student_fee_id' => $studentFee->id,
                'parent_id' => $parent->id,
                'school_id' => $studentFee->student->school_id,
                'amount' => $studentFee->amount,
                'payment_method' => 'credit_card',
                'status' => 'pending',
                'transaction_id' => 'TXN_' . time() . '_' . $studentFee->id,
            ]);

            $school = $studentFee->student->school;
            if (!$school->iyzico_sub_merchant_key) {
                DB::rollBack();
                return response()->json(['message' => 'Okulun ödeme entegrasyonu yapılandırılmamış.'], 500);
            }

            $commissionRate = $school->getEffectiveCommissionRate();
            $commissionAmount = $studentFee->amount * ($commissionRate / 100);
            $subMerchantPrice = $studentFee->amount - $commissionAmount;

            $paymentData = [
                'conversation_id' => 'CONV_' . time() . '_' . $payment->id,
                'amount' => $studentFee->amount,
                'basket_id' => 'BASKET_' . $studentFee->id,
                'basket_item_id' => 'ITEM_' . $studentFee->id,
                'basket_item_name' => $studentFee->fee_label . ' - ' . $studentFee->student->first_name . ' ' . $studentFee->student->last_name,
                'card_holder_name' => $validated['card_holder_name'],
                'card_number' => str_replace(' ', '', $validated['card_number']),
                'expire_month' => $validated['expire_month'],
                'expire_year' => $validated['expire_year'],
                'cvc' => $validated['cvc'],
                'buyer_id' => $parent->id,
                'buyer_name' => explode(' ', $user->name)[0] ?? $user->name,
                'buyer_surname' => explode(' ', $user->name)[1] ?? '',
                'buyer_phone' => $parent->phone ?? '5550000000',
                'buyer_email' => $user->email,
                'buyer_address' => $parent->address ?? '',
                'buyer_identity_number' => $parent->identity_number,
            ];

            $iyzicoResult = $this->iyzicoService->createMarketplacePayment(
                $paymentData,
                $school->iyzico_sub_merchant_key,
                $subMerchantPrice
            );

            if ($iyzicoResult['success']) {
                $payment->update([
                    'status' => 'completed',
                    'iyzico_payment_id' => $iyzicoResult['payment_id'],
                    'paid_at' => now(),
                ]);

                $studentFee->update(['status' => 'paid']);

                $bankAccount = $school->bankAccounts()->where('is_active', true)->first();
                if ($bankAccount) {
                    \App\Models\Distribution::create([
                        'school_id' => $school->id,
                        'bank_account_id' => $bankAccount->id,
                        'amount' => $payment->amount,
                        'commission' => $commissionAmount,
                        'net_amount' => $subMerchantPrice,
                        'status' => 'completed',
                        'iyzico_transfer_id' => $iyzicoResult['payment_id'],
                        'processed_at' => now(),
                        'notes' => 'Iyzico pazaryeri otomatik dağıtım',
                    ]);
                }

                DB::commit();

                $parentPhone = $parent->phone ?? null;
                if ($parentPhone) {
                    $studentName = $studentFee->student->first_name . ' ' . $studentFee->student->last_name;
                    $msg = "{$studentName} aidati (" . number_format($studentFee->amount, 2) . " TL) alindi. Tesekkurler. Spordosyam";
                    app(SmsNotificationService::class)->sendIfEnabled('payment_received', $parentPhone, $msg, $parent->user);
                }

                return response()->json([
                    'message' => 'Ödeme başarıyla tamamlandı.',
                    'payment_id' => $payment->id,
                    'fee' => [
                        'id' => $studentFee->id,
                        'status' => $studentFee->status,
                    ],
                ]);
            }

            $payment->update([
                'status' => 'failed',
                'notes' => $iyzicoResult['error'] ?? 'Ödeme işlemi başarısız',
            ]);

            DB::commit();

            return response()->json([
                'message' => $iyzicoResult['error'] ?? 'Ödeme işlemi başarısız oldu.',
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Ödeme işlemi sırasında bir hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }
}

