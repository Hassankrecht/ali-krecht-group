<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('Your Review Has Been Approved')
            ->greeting('Hello ' . $this->review->name . ',')
            ->line('Thank you for submitting a review!')
            ->line('Your review has been approved and is now visible to other customers.')
            ->line('Review: ' . $this->review->review)
            ->line('Rating: ' . str_repeat('⭐', $this->review->rating))
            ->line('Thank you for your feedback!')
            ->action('View Review', url('/reviews/' . $this->review->id));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'rating' => $this->review->rating,
            'message' => 'Your review has been approved',
        ];
    }
}
