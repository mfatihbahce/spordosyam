<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'school_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function coach()
    {
        return $this->hasOne(Coach::class);
    }

    public function parent()
    {
        return $this->hasOne(ParentModel::class);
    }

    // Helper methods
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCoach(): bool
    {
        return $this->role === 'coach';
    }

    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    /** Panel kullanıcısı (admin/coach/parent) için okul; lisans kontrolünde kullanılır. */
    public function getSchoolForLicense(): ?School
    {
        if ($this->role === 'admin' && $this->school_id) {
            return $this->school;
        }
        if ($this->role === 'coach' && $this->coach) {
            return $this->coach->school;
        }
        if ($this->role === 'parent' && $this->parent) {
            return $this->parent->school;
        }
        return null;
    }
}
