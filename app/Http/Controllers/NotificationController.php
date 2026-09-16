<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread count and latest notifications for the header dropdown (AJAX)
     */
    public function dropdown(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['unread_count' => 0, 'notifications' => []]);
        }

        $unreadCount = AppNotification::where('user_id', $userId)->unread()->count();
        $notifications = AppNotification::where('user_id', $userId)
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'message' => $item->message,
                    'type' => $item->type,
                    'icon' => $item->icon ?: 'fas fa-bell',
                    'action_url' => $item->action_url,
                    'is_read' => $item->is_read,
                    'time_ago' => $item->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a single notification as read and redirect to its target URL
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = AppNotification::where('user_id', Auth::id())->findOrFail($id);
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
            ]);
        }

        if (!empty($notification->action_url)) {
            return redirect($notification->action_url);
        }

        return redirect()->back();
    }

    /**
     * Mark all notifications for the authenticated user as read
     */
    public function markAllAsRead(Request $request)
    {
        AppNotification::where('user_id', Auth::id())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
            ]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Admin Notifications History Page
     */
    public function adminIndex(Request $request)
    {
        $query = AppNotification::where('user_id', Auth::id());

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->read();
            }
        }

        $notifications = $query->latest()->paginate(20)->withQueryString();
        $unreadCount = AppNotification::where('user_id', Auth::id())->unread()->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * User Notifications History Page
     */
    public function userIndex(Request $request)
    {
        $query = AppNotification::where('user_id', Auth::id());

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->read();
            }
        }

        $notifications = $query->latest()->paginate(20)->withQueryString();
        $unreadCount = AppNotification::where('user_id', Auth::id())->unread()->count();

        return view('user.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = AppNotification::where('user_id', Auth::id())->findOrFail($id);
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted',
        ]);
    }
}

