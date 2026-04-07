<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseExtension extends Model
{
    protected $fillable = [
        'school_id',
        'extended_at',
        'days_added',
        'amount',
        'extended_by',
    ];

    protected function casts(): array
    {
        return [
            'extended_at' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function extendedByUser()
    {
        return $this->belongsTo(User::class, 'extended_by');
    }
}
