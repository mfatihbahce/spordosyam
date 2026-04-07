<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Veli bildirimlerini listele (SMS ile gönderilen bildirimler mobilde de gösterilir).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Yetkisiz.'], 401);
        }

        $perPage = min((int) ($request->get('per_page', 20)), 50);
        $notifications = $user->notifications()
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'notifications' => $notifications->map(function ($n) {
                $data = $n->data;
                return [
                    'id' => $n->id,
                    'type' => $data['type'] ?? null,
                    'title' => $data['title'] ?? 'Bildirim',
                    'message' => $data['message'] ?? '',
                    'read_at' => $n->read_at?->toIso8601String(),
                    'created_at' => $n->created_at->toIso8601String(),
                ];
            })->values(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    /**
     * Bildirimi okundu işaretle.
     */
    public function markAsRead(string $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json(['message' => 'Bildirim bulunamadı.'], 404);
        }

        $notification->markAsRead();

        return response()->json(['message' => 'Okundu işaretlendi.']);
    }

    /**
     * Tüm bildirimleri okundu işaretle.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'Tüm bildirimler okundu işaretlendi.']);
    }
}
