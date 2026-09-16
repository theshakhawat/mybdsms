<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->get();
        $unreadCount = ContactMessage::unread()->count();
        return view('admin.messages.index', compact('messages', 'unreadCount'));
    }

    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->markAsRead();

        return response()->json([
            'success' => true,
            'data' => $message
        ]);
    }

    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully'
        ]);
    }

    public function markAsRead($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read'
        ]);
    }

    public function markAllAsRead()
    {
        ContactMessage::unread()->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All messages marked as read'
        ]);
    }

    public function getUnreadCount()
    {
        $count = ContactMessage::unread()->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}
