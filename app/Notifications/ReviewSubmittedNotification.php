<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Review $review;

    /**
     * Create a new notification instance.
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Review Pending Approval')
            ->greeting('Hello Admin,')
            ->line('A new review has been submitted and is awaiting approval.')
            ->line('Author: ' . $this->review->name)
            ->line('Profession: ' . $this->review->profession)
            ->line('Rating: ' . str_repeat('⭐', $this->review->rating))
            ->line('Review: ' . $this->review->review)
            ->action('Review & Approve', url('/admin/reviews/' . $this->review->id))
            ->line('Please review and approve if appropriate.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'author' => $this->review->name,
            'rating' => $this->review->rating,
            'message' => 'New review awaiting approval',
        ];
    }
}
