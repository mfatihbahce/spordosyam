<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentClassHistory extends Model
{
    protected $table = 'student_class_history';

    protected $fillable = ['student_id', 'class_id', 'enrolled_at', 'left_at', 'total_credits', 'used_credits', 'leave_reason'];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'left_at' => 'datetime',
            'total_credits' => 'integer',
            'used_credits' => 'integer',
        ];
    }

    public function getRemainingCreditsAttribute(): int
    {
        return max(0, ($this->total_credits ?? 0) - ($this->used_credits ?? 0));
    }

    /** Mezun mu (dersi bitirdi) */
    public function getIsGraduatedAttribute(): bool
    {
        return $this->leave_reason === 'graduated';
    }

    /** Ayrıldı mı (transfer) */
    public function getIsTransferredAttribute(): bool
    {
        return $this->leave_reason === 'transferred';
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /** Mevcut kayıt (sınıftan ayrılmamış) */
    public function scopeCurrent($query)
    {
        return $query->whereNull('left_at');
    }
}
