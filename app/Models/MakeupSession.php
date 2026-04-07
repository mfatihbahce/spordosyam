<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MakeupSession extends Model
{
    protected $fillable = [
        'school_id',
        'branch_id',
        'coach_id',
        'scheduled_date',
        'start_time',
        'end_time',
        'name',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function coach()
    {
        return $this->belongsTo(Coach::class);
    }

    public function studentMakeupClasses()
    {
        return $this->hasMany(StudentMakeupClass::class, 'makeup_session_id');
    }
}
