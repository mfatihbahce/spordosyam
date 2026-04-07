<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MakeupClass extends Model
{
    protected $fillable = [
        'school_id',
        'cancellation_id',
        'original_class_id',
        'scheduled_class_id',
        'scheduled_date',
        'type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'type' => 'string',
            'status' => 'string',
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function cancellation()
    {
        return $this->belongsTo(ClassCancellation::class, 'cancellation_id');
    }

    public function originalClass()
    {
        return $this->belongsTo(ClassModel::class, 'original_class_id');
    }

    public function scheduledClass()
    {
        return $this->belongsTo(ClassModel::class, 'scheduled_class_id');
    }

    public function studentMakeupClasses()
    {
        return $this->hasMany(StudentMakeupClass::class);
    }
}
