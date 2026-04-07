<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassCancellation extends Model
{
    protected $fillable = [
        'class_id',
        'school_id',
        'cancelled_by_user_id',
        'cancellation_type',
        'original_date',
        'new_date',
        'reason',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'original_date' => 'date',
            'new_date' => 'date',
            'cancellation_type' => 'string',
            'status' => 'string',
        ];
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by_user_id');
    }

    public function makeupClasses()
    {
        return $this->hasMany(MakeupClass::class, 'cancellation_id');
    }
}
