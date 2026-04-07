<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'school_id',
        'branch_id',
        'sport_branch_id',
        'coach_id',
        'name',
        'description',
        'capacity',
        'class_schedule',
        'class_days',
        'end_date',
        'is_active',
        'default_credits',
    ];

    protected function casts(): array
    {
        return [
            'class_days' => 'array',
            'class_schedule' => 'array',
            'end_date' => 'date',
            'is_active' => 'boolean',
            'default_credits' => 'integer',
        ];
    }

    /**
     * Bitiş tarihi geçmiş sınıfları otomatik kapat
     */
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->where('is_active', true)
              ->where(function($subQ) {
                  $subQ->whereNull('end_date')
                       ->orWhere('end_date', '>=', now()->toDateString());
              });
        });
    }

    /**
     * Sınıfın aktif olup olmadığını kontrol et (bitiş tarihi dahil)
     */
    public function getIsActuallyActiveAttribute()
    {
        if (!$this->is_active) {
            return false;
        }
        
        if ($this->end_date && $this->end_date < now()->toDateString()) {
            return false;
        }
        
        return true;
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function sportBranch()
    {
        return $this->belongsTo(SportBranch::class, 'sport_branch_id');
    }

    public function coach()
    {
        return $this->belongsTo(Coach::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    /** Bu sınıfta hâlâ kayıtlı olan öğrenciler (left_at boş = aktif kayıt) */
    public function currentEnrollments()
    {
        return $this->hasMany(StudentClassHistory::class, 'class_id')->whereNull('left_at');
    }

    /** Bu sınıfta kayıtlı olmuş öğrenciler (mezun / ayrılmış, left_at dolu) */
    public function pastEnrollments()
    {
        return $this->hasMany(StudentClassHistory::class, 'class_id')->whereNotNull('left_at');
    }

    public function cancellations()
    {
        return $this->hasMany(ClassCancellation::class);
    }

    public function makeupClasses()
    {
        return $this->hasMany(MakeupClass::class, 'original_class_id');
    }
}
