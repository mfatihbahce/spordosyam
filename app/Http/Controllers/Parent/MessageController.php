<?php

namespace App\Http\Controllers\Parent;

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
        $parent = Auth::user()->parent;
        if (!$parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Veli bilgileriniz bulunamadı.');
        }

        $conversations = ParentCoachConversation::where('parent_id', $parent->id)
            ->with(['coach.user', 'student', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->get();

        return view('parent.messages.index', compact('conversations'));
    }

    public function create()
    {
        $parent = Auth::user()->parent;
        if (!$parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Veli bilgileriniz bulunamadı.');
        }

        $students = $parent->students()->with(['currentEnrollments.classModel.coach.user'])->get();
        return view('parent.messages.create', compact('students'));
    }

    public function store(Request $request)
    {
        $parent = Auth::user()->parent;
        if (!$parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Veli bilgileriniz bulunamadı.');
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'coach_id' => 'required|exists:coaches,id',
            'body' => 'required|string|max:2000',
        ]);

        $studentId = (int) $validated['student_id'];
        $coachId = (int) $validated['coach_id'];

        if (!$parent->students()->where('students.id', $studentId)->exists()) {
            return back()->with('error', 'Bu öğrenci size ait değil.');
        }

        $classHasCoach = \App\Models\ClassModel::where('coach_id', $coachId)
            ->whereHas('currentEnrollments', fn($q) => $q->where('student_id', $studentId))
            ->exists();
        if (!$classHasCoach) {
            return back()->with('error', 'Bu antrenör bu öğrencinin antrenörü değil.');
        }

        $conversation = ParentCoachConversation::firstOrCreate(
            [
                'parent_id' => $parent->id,
                'coach_id' => $coachId,
                'student_id' => $studentId,
            ],
            ['last_message_at' => now()]
        );

        ParentCoachMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'parent',
            'sender_id' => $parent->id,
            'body' => $validated['body'],
        ]);
        $conversation->update(['last_message_at' => now()]);

        $conversation->load('coach');
        if ($conversation->coach && !empty($conversation->coach->phone)) {
            app(SmsNotificationService::class)->sendIfEnabled('coach_parent_message', $conversation->coach->phone, 'Bir veli size mesaj gonderdi. Panelden kontrol edin. Spordosyam');
        }

        return redirect()->route('parent.messages.show', $conversation)->with('success', 'Mesajınız gönderildi.');
    }

    public function show(ParentCoachConversation $message)
    {
        $parent = Auth::user()->parent;
        if (!$parent || $message->parent_id !== $parent->id) {
            abort(403);
        }

        $message->load(['coach.user', 'student', 'messages']);
        $message->messages()->where('sender_type', 'coach')->whereNull('read_at')->update(['read_at' => now()]);

        return view('parent.messages.show', compact('message'));
    }

    public function reply(Request $request, ParentCoachConversation $message)
    {
        $parent = Auth::user()->parent;
        if (!$parent || $message->parent_id !== $parent->id) {
            abort(403);
        }

        $validated = $request->validate(['body' => 'required|string|max:2000']);

        ParentCoachMessage::create([
            'conversation_id' => $message->id,
            'sender_type' => 'parent',
            'sender_id' => $parent->id,
            'body' => $validated['body'],
        ]);
        $message->update(['last_message_at' => now()]);

        $message->load('coach');
        if ($message->coach && !empty($message->coach->phone)) {
            app(SmsNotificationService::class)->sendIfEnabled('coach_parent_message', $message->coach->phone, 'Bir veli size mesaj gonderdi. Panelden kontrol edin. Spordosyam');
        }

        return redirect()->route('parent.messages.show', $message)->with('success', 'Mesajınız gönderildi.');
    }
}
