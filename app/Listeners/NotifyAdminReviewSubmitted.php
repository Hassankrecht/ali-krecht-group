<?php

namespace App\Listeners;

use App\Events\ReviewCreated;
use App\Notifications\ReviewSubmittedNotification;
use App\Models\Admin\Admin;
use Illuminate\Support\Facades\Notification;

class NotifyAdminReviewSubmitted
{
    /**
     * Handle ReviewCreated event - Notify admin of new pending review
     */
    public function handle(ReviewCreated $event): void
    {
        // Get all admins
        $admins = Admin::where('is_active', true)->get();

        // Send notification to all admins
        Notification::send($admins, new ReviewSubmittedNotification($event->review));
    }
}
