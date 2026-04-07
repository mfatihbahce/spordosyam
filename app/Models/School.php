<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'phone',
        'email',
        'address',
        'logo',
        'is_active',
        'is_demo',
        'demo_expires_at',
        'iyzico_api_key',
        'iyzico_secret_key',
        'iyzico_sub_merchant_key',
        'iyzico_commission_rate',
        'makeup_class_enabled',
        'license_type',
        'paid_amount',
        'license_extended_count',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_demo' => 'boolean',
            'demo_expires_at' => 'date',
            'iyzico_commission_rate' => 'decimal:2',
            'makeup_class_enabled' => 'boolean',
            'paid_amount' => 'decimal:2',
        ];
    }

    /** Lisans bitiş tarihi (demo_expires_at tek kaynak) */
    public function getLicenseExpiresAtAttribute()
    {
        return $this->demo_expires_at;
    }

    /** Lisans süresi dolmuş mu? */
    public function isLicenseExpired(): bool
    {
        if (!$this->demo_expires_at) {
            return false;
        }
        return $this->demo_expires_at->endOfDay()->isPast();
    }

    /** Lisans bitimine kalan gün sayısı (negatif = dolmuş) */
    public function getDaysUntilLicenseExpires(): ?int
    {
        if (!$this->demo_expires_at) {
            return null;
        }
        return (int) now()->startOfDay()->diffInDays($this->demo_expires_at->startOfDay(), false);
    }

    /** 10 gün veya daha az kala mı? (uyarı göstermek için) */
    public function isLicenseExpiringSoon(): bool
    {
        $days = $this->getDaysUntilLicenseExpires();
        return $days !== null && $days >= 0 && $days <= 10;
    }

    /** Lisans türü etiketi */
    public function getLicenseTypeLabelAttribute(): string
    {
        return match ($this->license_type) {
            'demo' => 'Demo',
            'free' => 'Ücretsiz',
            'paid' => 'Ücretli',
            default => $this->license_type ?? '—',
        };
    }

    /** Sadece aktif lisansı olan okullar (bitiş yok veya gelecekte) */
    public function scopeActiveLicense($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('demo_expires_at')
                ->orWhere('demo_expires_at', '>', now()->endOfDay());
        });
    }

    /** Lisans süresi dolmuş okullar */
    public function scopeExpiredLicense($query)
    {
        return $query->whereNotNull('demo_expires_at')
            ->where('demo_expires_at', '<=', now()->endOfDay());
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function sportBranches()
    {
        return $this->hasMany(SportBranch::class);
    }

    public function classes()
    {
        return $this->hasMany(ClassModel::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function coaches()
    {
        return $this->hasMany(Coach::class);
    }

    public function parents()
    {
        return $this->hasMany(ParentModel::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function licenseExtensions()
    {
        return $this->hasMany(LicenseExtension::class);
    }

    /** Bu okuldan alınan toplam lisans uzatım ücreti (₺) */
    public function getTotalExtensionRevenueAttribute(): float
    {
        return (float) $this->licenseExtensions()->sum('amount');
    }

    /**
     * Ödeme hesaplamasında kullanılacak komisyon oranı (%).
     * Okula özel oran varsa onu, yoksa Ödeme Ayarları'ndaki varsayılan oranı döner.
     */
    public function getEffectiveCommissionRate(): float
    {
        if ($this->iyzico_commission_rate !== null && $this->iyzico_commission_rate !== '') {
            return (float) $this->iyzico_commission_rate;
        }
        return (float) (env('DEFAULT_COMMISSION_RATE', 5));
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }
}
