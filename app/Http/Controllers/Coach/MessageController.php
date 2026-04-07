<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\ParentCoachConversation;
use App\Models\ParentCoachMessage;
use App\Services\SmsNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $coach = Auth::user()->coach;
        if (!$coach) {
            return redirect()->route('coach.dashboard')->with('error', 'Antrenör bilgileriniz bulunamadı.');
        }

        $conversations = ParentCoachConversation::where('coach_id', $coach->id)
            ->with(['parent.user', 'student', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->get();

        return view('coach.messages.index', compact('conversations'));
    }

    public function show(ParentCoachConversation $message)
    {
        $coach = Auth::user()->coach;
        if (!$coach || $message->coach_id !== $coach->id) {
            abort(403);
        }

        $message->load(['parent.user', 'student', 'messages']);
        $message->messages()->where('sender_type', 'parent')->whereNull('read_at')->update(['read_at' => now()]);

        return view('coach.messages.show', compact('message'));
    }

    public function reply(Request $request, ParentCoachConversation $message)
    {
        $coach = Auth::user()->coach;
        if (!$coach || $message->coach_id !== $coach->id) {
            abort(403);
        }

        $validated = $request->validate(['body' => 'required|string|max:2000']);

        ParentCoachMessage::create([
            'conversation_id' => $message->id,
            'sender_type' => 'coach',
            'sender_id' => $coach->id,
            'body' => $validated['body'],
        ]);
        $message->update(['last_message_at' => now()]);

        $parent = $message->parent;
        if ($parent && !empty($parent->phone)) {
            app(SmsNotificationService::class)->sendIfEnabled('coach_new_message', $parent->phone, 'Antrenorunuzden yeni mesaj var. Panelden kontrol edin. Spordosyam', $parent->user);
        }

        return redirect()->route('coach.messages.show', $message)->with('success', 'Mesajınız gönderildi.');
    }
}
