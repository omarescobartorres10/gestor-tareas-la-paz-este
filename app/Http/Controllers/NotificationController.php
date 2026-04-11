<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Task;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the user
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()
            ->with(['task:id,title', 'comment:id,content'])
            ->select('notifications.*')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read and redirect to task
     */
    public function markAsRead(Request $request, Notification $notification)
    {

        // Ensure user owns this notification
        if ($notification->user_id !== auth()->id()) {
            \Log::warning('Unauthorized notification access', [
                'notification_id' => $notification->id,
                'owner_id' => $notification->user_id,
                'accessor_id' => auth()->id(),
                'ip' => $request->ip()
            ]);
            abort(403);
        }

        $notification->markAsRead();

        // Invalidate notification caches
        \Cache::forget("notifications.unread.{$notification->user_id}");
        \Cache::forget("notifications.recent.{$notification->user_id}");

        // Check if task exists before redirecting
        if ($notification->task_id && Task::where('id', $notification->task_id)->exists()) {
            return redirect()->route('tasks.show', $notification->task_id);
        }

        // Task was deleted or doesn't exist - redirect to notifications with message
        return redirect()->route('notifications.index')
            ->with('warning', 'La tarea asociada a esta notificación ya no existe.');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $userId = auth()->id();

        auth()->user()->unreadNotifications()
            ->update(['is_read' => true]);

        // Invalidate caches
        \Cache::forget("notifications.unread.{$userId}");
        \Cache::forget("notifications.recent.{$userId}");

        return redirect()->back()->with('success', 'Todas las notificaciones marcadas como leídas');
    }

    /**
     * Get unread notification count (AJAX)
     */
    public function getUnreadCount()
    {
        $userId = auth()->id();

        // Cache unread count for 1 minute
        $count = \Cache::remember("notifications.unread.{$userId}", 60, function () {
            return auth()->user()->unreadNotificationsCount();
        });

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications (AJAX)
     */
    public function getRecent()
    {
        $userId = auth()->id();

        // Cache recent notifications for 30 seconds
        $notifications = \Cache::remember("notifications.recent.{$userId}", 30, function () {
            return auth()->user()->notifications()
                ->with(['task:id,title', 'comment:id,content,user_id', 'comment.user:id,name'])
                ->select('notifications.*')
                ->latest()
                ->take(10)
                ->get();
        });

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    /**
     * Toggle email notifications for current user
     */
    public function toggleEmailNotifications(Request $request)
    {
        $user = auth()->user();
        $user->email_notifications = !$user->email_notifications;
        $user->save();

        $status = $user->email_notifications ? 'activadas' : 'desactivadas';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'enabled' => $user->email_notifications,
                'message' => "Notificaciones por correo {$status}"
            ]);
        }

        return redirect()->back()->with('success', "Notificaciones por correo {$status}");
    }
}
