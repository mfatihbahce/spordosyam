<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentMakeupClass extends Model
{
    protected $fillable = [
        'student_id',
        'makeup_class_id',
        'attendance_id',
        'scheduled_class_id',
        'makeup_session_id',
        'scheduled_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'status' => 'string',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function makeupClass()
    {
        return $this->belongsTo(MakeupClass::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function scheduledClass()
    {
        return $this->belongsTo(ClassModel::class, 'scheduled_class_id');
    }

    public function makeupSession()
    {
        return $this->belongsTo(MakeupSession::class, 'makeup_session_id');
    }
}
