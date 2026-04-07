<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePlan extends Model
{
    protected $fillable = [
        'school_id',
        'name',
        'amount',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function studentFees()
    {
        return $this->hasMany(StudentFee::class);
    }
}
