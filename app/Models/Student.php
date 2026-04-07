<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'school_id',
        'class_id',
        'first_name',
        'last_name',
        'identity_number',
        'birth_date',
        'gender',
        'phone',
        'email',
        'address',
        'notes',
        'is_active',
        'class_credit',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function parents()
    {
        return $this->belongsToMany(ParentModel::class, 'student_parent', 'student_id', 'parent_id')
            ->withPivot('relationship', 'is_primary')
            ->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function studentFees()
    {
        return $this->hasMany(StudentFee::class);
    }

    public function progress()
    {
        return $this->hasMany(StudentProgress::class);
    }

    public function makeupClasses()
    {
        return $this->hasMany(StudentMakeupClass::class);
    }

    public function classHistory()
    {
        return $this->hasMany(StudentClassHistory::class)->orderByDesc('enrolled_at');
    }

    /** Aktif derse kayıtlar (left_at boş) */
    public function currentEnrollments()
    {
        return $this->hasMany(StudentClassHistory::class)->whereNull('left_at');
    }

    /**
     * Öğrenci hem aktif hem de sınıfı aktifse "efektif aktif"
     */
    public function getEffectiveIsActiveAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        $class = $this->relationLoaded('classModel') ? $this->classModel : $this->classModel()->first();
        if (!$class) {
            return true;
        }
        return (bool) ($class->is_actually_active ?? ($class->is_active && (!$class->end_date || $class->end_date >= now()->toDateString())));
    }
}
