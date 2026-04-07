<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'student_fee_id',
        'parent_id',
        'school_id',
        'amount',
        'payment_method',
        'status',
        'transaction_id',
        'iyzico_payment_id',
        'notes',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function parent()
    {
        return $this->belongsTo(ParentModel::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
