<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'sms_balance',
        'profile_image',
        'nid_front',
        'nid_back',
        'trade_license',
        'kyc_status',
        'rejection_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
            'sms_balance' => 'integer',
        ];
    }

    /**
     * Get user invoices.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get user orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get user payments.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get user app notifications.
     */
    public function appNotifications()
    {
        return $this->hasMany(AppNotification::class);
    }

    /**
     * Get user unread notifications count.
     */
    public function unreadNotificationsCount(): int
    {
        return $this->appNotifications()->unread()->count();
    }
}
