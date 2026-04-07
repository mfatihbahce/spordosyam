<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'school_id',
        'account_holder_name',
        'iban',
        'bank_name',
        'branch_name',
        'sub_merchant_type',
        'tax_office',
        'tax_number',
        'legal_company_title',
        'identity_number',
        'contact_name',
        'contact_surname',
        'gsm_number',
        'email',
        'address',
        'is_verified',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }
}
