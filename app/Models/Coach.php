<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
    protected $fillable = [
        'school_id',
        'user_id',
        'phone',
        'bio',
        'certificates',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'certificates' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classes()
    {
        return $this->hasMany(ClassModel::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function progress()
    {
        return $this->hasMany(StudentProgress::class);
    }

    public function parentCoachConversations()
    {
        return $this->hasMany(ParentCoachConversation::class, 'coach_id');
    }
}
