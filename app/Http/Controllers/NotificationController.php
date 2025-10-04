<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            $notifications = $user->notifications()
                ->latest()
                ->paginate(20);

            return view('notifications.index', compact('notifications'));

        } catch (\Exception $e) {
            \Log::error('Error en NotificationController@index: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar notificaciones');
        }
    }

    public function markAsRead($id)
    {
        try {
            $notification = Notification::where('user_id', Auth::id())
                ->where('id', $id)
                ->firstOrFail();

            $notification->markAsRead();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error('Error en NotificationController@markAsRead: ' . $e->getMessage());
            return response()->json(['error' => 'Notificación no encontrada'], 404);
        }
    }

    public function markAllAsRead()
    {
        try {
            Auth::user()->notifications()
                ->unread()
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error('Error en NotificationController@markAllAsRead: ' . $e->getMessage());
            return response()->json(['error' => 'Error al marcar notificaciones'], 500);
        }
    }

    public function getUnreadCount()
    {
        try {
            $count = Auth::user()->notifications()
                ->unread()
                ->count();

            return response()->json(['count' => $count]);

        } catch (\Exception $e) {
            \Log::error('Error en NotificationController@getUnreadCount: ' . $e->getMessage());
            return response()->json(['count' => 0]);
        }
    }

    public function getNotifications()
    {
        try {
            $notifications = Auth::user()->notifications()
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'is_read' => $notification->is_read,
                        'created_at' => $notification->created_at->diffForHumans(),
                        'url' => $notification->getUrl()
                    ];
                });

            return response()->json($notifications);

        } catch (\Exception $e) {
            \Log::error('Error en NotificationController@getNotifications: ' . $e->getMessage());
            return response()->json([]);
        }
    }
}