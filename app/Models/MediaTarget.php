<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaTarget extends Model
{
    protected $fillable = [
        'media_id',
        'target_type',
        'target_id',
    ];

    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
