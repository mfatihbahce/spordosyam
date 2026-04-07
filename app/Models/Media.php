<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'school_id',
        'uploaded_by',
        'uploader_type',
        'title',
        'description',
        'type',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'visibility',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function targets()
    {
        return $this->hasMany(MediaTarget::class);
    }
}
