<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    protected $fillable = [
        'student_id',
        'fee_plan_id',
        'amount',
        'due_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feePlan()
    {
        return $this->belongsTo(FeePlan::class);
    }

    /** Plan yoksa "Aidat" olarak göster (öğrenciye manuel tanımlanan aidat) */
    public function getFeeLabelAttribute(): string
    {
        return $this->feePlan?->name ?? 'Aidat';
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
