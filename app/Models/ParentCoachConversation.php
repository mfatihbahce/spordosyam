<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentCoachConversation extends Model
{
    protected $table = 'parent_coach_conversations';

    protected $fillable = ['parent_id', 'coach_id', 'student_id', 'last_message_at'];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    public function coach()
    {
        return $this->belongsTo(Coach::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function messages()
    {
        return $this->hasMany(ParentCoachMessage::class, 'conversation_id')->orderBy('created_at');
    }
}
