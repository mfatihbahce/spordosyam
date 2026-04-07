<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentCoachMessage extends Model
{
    protected $table = 'parent_coach_messages';

    protected $fillable = ['conversation_id', 'sender_type', 'sender_id', 'body', 'read_at'];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function conversation()
    {
        return $this->belongsTo(ParentCoachConversation::class, 'conversation_id');
    }

    public function isFromParent(): bool
    {
        return $this->sender_type === 'parent';
    }

    public function isFromCoach(): bool
    {
        return $this->sender_type === 'coach';
    }

    public function getSenderNameAttribute(): string
    {
        if ($this->sender_type === 'parent') {
            $parent = ParentModel::find($this->sender_id);
            return $parent?->user?->name ?? 'Veli';
        }
        $coach = Coach::find($this->sender_id);
        return $coach?->user?->name ?? 'Antrenör';
    }
}
