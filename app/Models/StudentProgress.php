<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProgress extends Model
{
    protected $fillable = [
        'student_id',
        'coach_id',
        'class_id',
        'notes',
        'progress_type',
        'progress_date',
    ];

    protected function casts(): array
    {
        return [
            'progress_date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function coach()
    {
        return $this->belongsTo(Coach::class);
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}
