<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    use HasFactory;

    protected $table = 'app_notifications';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'icon',
        'action_url',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * The user that the notification belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Send notification to all admin users.
     */
    public static function sendToAdmins(string $title, string $message, string $type = 'general', ?string $actionUrl = null, ?string $icon = null): void
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            self::create([
                'user_id' => $admin->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'icon' => $icon ?: 'fas fa-bell text-primary',
                'action_url' => $actionUrl,
                'is_read' => false,
            ]);
        }
    }

    /**
     * Send notification to all regular users.
     */
    public static function sendToAllUsers(string $title, string $message, string $type = 'general', ?string $actionUrl = null, ?string $icon = null): void
    {
        $users = User::where('role', 'user')->get();
        foreach ($users as $user) {
            self::create([
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'icon' => $icon ?: 'fas fa-bell text-info',
                'action_url' => $actionUrl,
                'is_read' => false,
            ]);
        }
    }

    /**
     * Send notification to a specific user.
     */
    public static function sendToUser(int $userId, string $title, string $message, string $type = 'general', ?string $actionUrl = null, ?string $icon = null): ?self
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => $icon ?: 'fas fa-bell text-success',
            'action_url' => $actionUrl,
            'is_read' => false,
        ]);
    }
}

