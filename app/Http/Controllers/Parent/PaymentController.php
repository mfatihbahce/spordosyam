<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\StudentFee;
use App\Models\Payment;
use App\Services\IyzicoService;
use App\Services\SmsNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentController extends Controller
{
    protected $iyzicoService;

    public function __construct(IyzicoService $iyzicoService)
    {
        $this->iyzicoService = $iyzicoService;
    }

    public function index()
    {
        $parent = Auth::user()->parent;
        
        if (!$parent) {
            $emptyPaginator = new LengthAwarePaginator([], 0, 15, 1);
            return view('parent.payments.index', [
                'studentFees' => $emptyPaginator
            ])->with('error', 'Veli bilgileriniz bulunamadı.');
        }
        
        $studentIds = $parent->students->pluck('id');
        
        if ($studentIds->isEmpty()) {
            $emptyPaginator = new LengthAwarePaginator([], 0, 15, 1);
            return view('parent.payments.index', [
                'studentFees' => $emptyPaginator
            ])->with('info', 'Henüz öğrenci atanmamış.');
        }
        
        $studentFees = StudentFee::whereIn('student_id', $studentIds)
            ->whereHas('student', fn ($q) => $q->where('is_active', true))
            ->where('status', '!=', 'paid')
            ->with(['student', 'feePlan', 'payments'])
            ->orderBy('due_date', 'asc')
            ->paginate(15);
        
        return view('parent.payments.index', compact('studentFees'));
    }

    public function create(StudentFee $studentFee)
    {
        $parent = Auth::user()->parent;
        
        if (!$parent) {
            abort(403, 'Veli bilgileriniz bulunamadı.');
        }
        
        // Veli'nin bu öğrenciye ait olduğunu kontrol et
        if (!$parent->students->contains($studentFee->student_id)) {
            abort(403);
        }
        
        // Ödeme zaten yapılmış mı kontrol et
        if ($studentFee->status === 'paid') {
            return redirect()->route('parent.payments.index')
                ->with('error', 'Bu aidat zaten ödenmiş.');
        }
        
        $studentFee->load(['student', 'feePlan']);
        
        return view('parent.payments.create', compact('studentFee'));
    }

    public function store(Request $request, StudentFee $studentFee)
    {
        $parent = Auth::user()->parent;
        $user = Auth::user();
        
        if (!$parent) {
            abort(403, 'Veli bilgileriniz bulunamadı.');
        }
        
        // Veli'nin bu öğrenciye ait olduğunu kontrol et
        if (!$parent->students->contains($studentFee->student_id)) {
            abort(403);
        }
        
        $validated = $request->validate([
            'card_holder_name' => 'required|string|max:255',
            'card_number' => 'required|string|min:16|max:19',
            'expire_month' => 'required|string|size:2',
            'expire_year' => 'required|string|size:4',
            'cvc' => 'required|string|min:3|max:4',
        ]);

        // TC Kimlik No ve Adres Iyzico için zorunlu; veli profilinden alınır (ödeme sayfasında istenmez)
        if (empty($parent->identity_number)) {
            return redirect()->route('parent.profile.index')
                ->with('error', 'Ödeme yapabilmek için lütfen profil bilgilerinize TC Kimlik No ekleyin. Profil sayfasında bir kez kaydettiğinizde ödemelerde otomatik kullanılır.');
        }
        if (empty(trim($parent->address ?? ''))) {
            return redirect()->route('parent.profile.index')
                ->with('error', 'Ödeme yapabilmek için lütfen profil bilgilerinize adres ekleyin. Iyzico alıcı adresi (kayıt adresi) zorunludur.');
        }

        try {
            DB::beginTransaction();

            // Ödeme kaydı oluştur
            $payment = Payment::create([
                'student_fee_id' => $studentFee->id,
                'parent_id' => $parent->id,
                'school_id' => $studentFee->student->school_id,
                'amount' => $studentFee->amount,
                'payment_method' => 'credit_card',
                'status' => 'pending',
                'transaction_id' => 'TXN_' . time() . '_' . $studentFee->id,
            ]);

            // Okul bilgilerini al
            $school = $studentFee->student->school;
            
            // Sub-merchant key kontrolü
            if (!$school->iyzico_sub_merchant_key) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->with('error', 'Okul için ödeme entegrasyonu henüz yapılandırılmamış. Lütfen okul yöneticisi ile iletişime geçin.');
            }

            // Komisyon hesaplama: okula özel oran yoksa Ödeme Ayarları'ndaki varsayılan oran kullanılır
            $commissionRate = $school->getEffectiveCommissionRate();
            $commissionAmount = $studentFee->amount * ($commissionRate / 100);
            $subMerchantPrice = $studentFee->amount - $commissionAmount; // Okula gidecek tutar

            // Iyzico pazaryeri ödeme işlemi (identityNumber zorunlu)
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

            // Pazaryeri ödemesi oluştur (Iyzico otomatik olarak komisyonu keser ve okula transfer eder)
            $iyzicoResult = $this->iyzicoService->createMarketplacePayment(
                $paymentData,
                $school->iyzico_sub_merchant_key,
                $subMerchantPrice
            );

            if ($iyzicoResult['success']) {
                // Ödeme başarılı: anında ödendi yap, bekleme yok
                $payment->update([
                    'status' => 'completed',
                    'iyzico_payment_id' => $iyzicoResult['payment_id'],
                    'paid_at' => now(),
                ]);

                // Aidatı hemen "ödendi" yap (otomatik, bekleme yok)
                $studentFee->update(['status' => 'paid']);

                // Dağıtım kaydı oluştur (Iyzico otomatik transfer yaptı, sadece kayıt tutuyoruz)
                $bankAccount = $school->bankAccounts()->where('is_active', true)->first();
                
                if ($bankAccount) {
                    \App\Models\Distribution::create([
                        'school_id' => $school->id,
                        'bank_account_id' => $bankAccount->id,
                        'amount' => $payment->amount,
                        'commission' => $commissionAmount,
                        'net_amount' => $subMerchantPrice,
                        'status' => 'completed', // Iyzico otomatik transfer etti
                        'iyzico_transfer_id' => $iyzicoResult['payment_id'], // Payment ID aynı zamanda transfer ID
                        'processed_at' => now(),
                        'notes' => 'Iyzico pazaryeri otomatik dağıtım',
                    ]);
                }

                DB::commit();

                // Ödeme alındı SMS (veli)
                $parentPhone = $parent->phone ?? null;
                if ($parentPhone) {
                    $studentName = $studentFee->student->first_name . ' ' . $studentFee->student->last_name;
                    $msg = "{$studentName} aidati (" . number_format($studentFee->amount, 2) . " TL) alindi. Tesekkurler. Spordosyam";
                    app(SmsNotificationService::class)->sendIfEnabled('payment_received', $parentPhone, $msg, $parent->user);
                }

                return redirect()->route('parent.payments.index')
                    ->with('success', 'Ödeme başarıyla tamamlandı.');
            } else {
                // Ödeme başarısız
                $payment->update([
                    'status' => 'failed',
                    'notes' => $iyzicoResult['error'] ?? 'Ödeme işlemi başarısız',
                ]);

                DB::commit();

                return back()
                    ->withInput()
                    ->with('error', $iyzicoResult['error'] ?? 'Ödeme işlemi başarısız oldu.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Ödeme işlemi sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }


    public function history()
    {
        $parent = Auth::user()->parent;
        
        if (!$parent) {
            $emptyPaginator = new LengthAwarePaginator([], 0, 15, 1);
            return view('parent.payments.history', [
                'payments' => $emptyPaginator
            ])->with('error', 'Veli bilgileriniz bulunamadı.');
        }
        
        $payments = Payment::where('parent_id', $parent->id)
            ->with(['studentFee.student', 'studentFee.feePlan'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('parent.payments.history', compact('payments'));
    }
}
