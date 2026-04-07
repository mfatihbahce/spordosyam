<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'school_name',
        'contact_name',
        'email',
        'phone',
        'address',
        'message',
        'password',
        'status',
        'approved_by',
        'approved_at',
        'demo_days',
        'demo_expires_at',
        'license_type',
        'paid_amount',
    ];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'demo_expires_at' => 'date',
            'paid_amount' => 'decimal:2',
        ];
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
