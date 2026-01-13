<?php

namespace App\Services;

use App\Models\User;
use App\Models\Admin\Admin;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Send notification to user
     */
    public function notifyUser(User $user, Notification $notification): void
    {
        $user->notify($notification);
    }

    /**
     * Send notification to multiple users
     */
    public function notifyUsers(array $users, Notification $notification): void
    {
        foreach ($users as $user) {
            $this->notifyUser($user, $notification);
        }
    }

    /**
     * Send notification to all users
     */
    public function notifyAllUsers(Notification $notification): void
    {
        $users = User::where('is_active', true)->get();
        NotificationFacade::send($users, $notification);
    }

    /**
     * Send notification to admin
     */
    public function notifyAdmin(Admin $admin, Notification $notification): void
    {
        $admin->notify($notification);
    }

    /**
     * Send notification to all admins
     */
    public function notifyAllAdmins(Notification $notification): void
    {
        $admins = Admin::where('is_active', true)->get();
        NotificationFacade::send($admins, $notification);
    }

    /**
     * Send notification to specific role
     */
    public function notifyRole(string $role, Notification $notification): void
    {
        $admins = Admin::where('role', $role)
            ->where('is_active', true)
            ->get();

        NotificationFacade::send($admins, $notification);
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead(User $user): void
    {
        $user->notifications->each->markAsRead();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notification): void
    {
        if (is_object($notification)) {
            $notification->markAsRead();
        }
    }

    /**
     * Delete notification
     */
    public function deleteNotification($notification): bool
    {
        return $notification->delete();
    }

    /**
     * Get user notifications
     */
    public function getUserNotifications(User $user, bool $unreadOnly = false)
    {
        $query = $user->notifications();

        if ($unreadOnly) {
            $query->whereNull('read_at');
        }

        return $query->latest()->paginate(15);
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Get admin notifications
     */
    public function getAdminNotifications(Admin $admin, bool $unreadOnly = false)
    {
        $query = $admin->notifications();

        if ($unreadOnly) {
            $query->whereNull('read_at');
        }

        return $query->latest()->paginate(15);
    }

    /**
     * Clear all notifications for user
     */
    public function clearUserNotifications(User $user): void
    {
        $user->notifications()->delete();
    }

    /**
     * Get notification by ID
    /**
     */
    public function getNotification($id)
    {
        return DB::table('notifications')->find($id);
    }

    /**
     * Send bulk notifications via queue
     */
    public function sendBulk(array $notifiable, Notification $notification): void
    {
        NotificationFacade::send($notifiable, $notification);
    }
}
