<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use App\Models\ParentCoachConversation;
use App\Models\ParentCoachMessage;
use App\Models\ClassModel;
use App\Services\SmsNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $parent = Auth::user()->parent;
        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        $conversations = ParentCoachConversation::where('parent_id', $parent->id)
            ->with(['coach.user', 'student', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'conversations' => $conversations->map(function ($c) {
                $last = $c->messages->first();
                return [
                    'id' => $c->id,
                    'student' => $c->student ? [
                        'id' => $c->student->id,
                        'name' => trim($c->student->first_name . ' ' . $c->student->last_name),
                    ] : null,
                    'coach' => $c->coach && $c->coach->user ? [
                        'id' => $c->coach->id,
                        'name' => $c->coach->user->name,
                    ] : null,
                    'last_message' => $last ? [
                        'id' => $last->id,
                        'sender_type' => $last->sender_type,
                        'body' => $last->body,
                        'created_at' => $last->created_at?->toIso8601String(),
                    ] : null,
                    'last_message_at' => $c->last_message_at?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    public function show(ParentCoachConversation $conversation)
    {
        $parent = Auth::user()->parent;
        if (!$parent || $conversation->parent_id !== $parent->id) {
            return response()->json(['message' => 'Yetkisiz.'], 403);
        }

        $conversation->load(['coach.user', 'student', 'messages']);
        $conversation->messages()
            ->where('sender_type', 'coach')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'student' => $conversation->student ? [
                    'id' => $conversation->student->id,
                    'name' => trim($conversation->student->first_name . ' ' . $conversation->student->last_name),
                ] : null,
                'coach' => $conversation->coach && $conversation->coach->user ? [
                    'id' => $conversation->coach->id,
                    'name' => $conversation->coach->user->name,
                ] : null,
            ],
            'messages' => $conversation->messages->map(function ($m) {
                return [
                    'id' => $m->id,
                    'sender_type' => $m->sender_type,
                    'body' => $m->body,
                    'created_at' => $m->created_at?->toIso8601String(),
                    'read_at' => $m->read_at?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    /**
     * Yeni görüşme + ilk mesaj (web store mantığının JSON versiyonu).
     */
    public function store(Request $request)
    {
        $parent = Auth::user()->parent;
        if (!$parent) {
            return response()->json(['message' => 'Veli bulunamadı.'], 404);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'coach_id' => 'required|exists:coaches,id',
            'body' => 'required|string|max:2000',
        ]);

        $studentId = (int) $validated['student_id'];
        $coachId = (int) $validated['coach_id'];

        if (!$parent->students()->where('students.id', $studentId)->exists()) {
            return response()->json(['message' => 'Bu öğrenci size ait değil.'], 422);
        }

        $classHasCoach = ClassModel::where('coach_id', $coachId)
            ->where(function ($q) use ($studentId) {
                $q->whereHas('currentEnrollments', fn($eq) => $eq->where('student_id', $studentId))
                  ->orWhereHas('students', fn($eq) => $eq->where('id', $studentId));
            })
            ->exists();
        if (!$classHasCoach) {
            return response()->json(['message' => 'Bu antrenör bu öğrencinin antrenörü değil.'], 422);
        }

        $conversation = ParentCoachConversation::firstOrCreate(
            [
                'parent_id' => $parent->id,
                'coach_id' => $coachId,
                'student_id' => $studentId,
            ],
            ['last_message_at' => now()]
        );

        $msg = ParentCoachMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'parent',
            'sender_id' => $parent->id,
            'body' => $validated['body'],
        ]);
        $conversation->update(['last_message_at' => now()]);

        $conversation->load('coach');
        if ($conversation->coach && !empty($conversation->coach->phone)) {
            app(SmsNotificationService::class)->sendIfEnabled(
                'coach_parent_message',
                $conversation->coach->phone,
                'Bir veli size mesaj gonderdi. Panelden kontrol edin. Spordosyam'
            );
        }

        return response()->json([
            'conversation_id' => $conversation->id,
            'message' => [
                'id' => $msg->id,
                'sender_type' => $msg->sender_type,
                'body' => $msg->body,
                'created_at' => $msg->created_at?->toIso8601String(),
            ],
        ], 201);
    }

    public function reply(Request $request, ParentCoachConversation $conversation)
    {
        $parent = Auth::user()->parent;
        if (!$parent || $conversation->parent_id !== $parent->id) {
            return response()->json(['message' => 'Yetkisiz.'], 403);
        }

        $validated = $request->validate(['body' => 'required|string|max:2000']);

        $msg = ParentCoachMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'parent',
            'sender_id' => $parent->id,
            'body' => $validated['body'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        $conversation->load('coach');
        if ($conversation->coach && !empty($conversation->coach->phone)) {
            app(SmsNotificationService::class)->sendIfEnabled(
                'coach_parent_message',
                $conversation->coach->phone,
                'Bir veli size mesaj gonderdi. Panelden kontrol edin. Spordosyam'
            );
        }

        return response()->json([
            'message' => [
                'id' => $msg->id,
                'sender_type' => $msg->sender_type,
                'body' => $msg->body,
                'created_at' => $msg->created_at?->toIso8601String(),
            ],
        ], 201);
    }
}

