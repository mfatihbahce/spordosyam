<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'makeup_session_id',
        'coach_id',
        'attendance_date',
        'attendance_time',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'attendance_time' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function coach()
    {
        return $this->belongsTo(Coach::class);
    }

    public function makeupSession()
    {
        return $this->belongsTo(MakeupSession::class, 'makeup_session_id');
    }
}
