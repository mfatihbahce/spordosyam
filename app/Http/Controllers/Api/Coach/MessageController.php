<?php

namespace App\Http\Controllers\Api\Coach;

use App\Http\Controllers\Controller;
use App\Models\ParentCoachConversation;
use App\Models\ParentCoachMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $coach = Auth::user()->coach;
        if (!$coach) {
            return response()->json(['message' => 'Antrenör bulunamadı.'], 404);
        }

        $conversations = ParentCoachConversation::where('coach_id', $coach->id)
            ->with(['parent.user', 'student', 'messages' => fn($q) => $q->latest()->limit(1)])
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
                    'parent' => $c->parent && $c->parent->user ? [
                        'id' => $c->parent->id,
                        'name' => $c->parent->user->name,
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
        $coach = Auth::user()->coach;
        if (!$coach || $conversation->coach_id !== $coach->id) {
            return response()->json(['message' => 'Yetkisiz.'], 403);
        }

        $conversation->load(['parent.user', 'student', 'messages']);
        $conversation->messages()
            ->where('sender_type', 'parent')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'student' => $conversation->student ? [
                    'id' => $conversation->student->id,
                    'name' => trim($conversation->student->first_name . ' ' . $conversation->student->last_name),
                ] : null,
                'parent' => $conversation->parent && $conversation->parent->user ? [
                    'id' => $conversation->parent->id,
                    'name' => $conversation->parent->user->name,
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

    public function reply(Request $request, ParentCoachConversation $conversation)
    {
        $coach = Auth::user()->coach;
        if (!$coach || $conversation->coach_id !== $coach->id) {
            return response()->json(['message' => 'Yetkisiz.'], 403);
        }

        $validated = $request->validate(['body' => 'required|string|max:2000']);

        $msg = ParentCoachMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'coach',
            'sender_id' => $coach->id,
            'body' => $validated['body'],
        ]);

        $conversation->update(['last_message_at' => now()]);

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

