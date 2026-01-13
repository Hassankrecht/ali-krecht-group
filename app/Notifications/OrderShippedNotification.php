<?php

namespace App\Notifications;

use App\Models\Checkout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderShippedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Checkout $checkout;

    /**
     * Create a new notification instance.
     */
    public function __construct(Checkout $checkout)
    {
        $this->checkout = $checkout;
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
            ->subject('Your Order Has Been Shipped #' . $this->checkout->id)
            ->greeting('Hello ' . $this->checkout->name . ',')
            ->line('Great news! Your order has been shipped.')
            ->line('Order ID: ' . $this->checkout->id)
            ->line('Tracking may be available shortly.')
            ->action('Track Order', url('/orders/' . $this->checkout->id . '/track'))
            ->line('Thank you for your patience!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'checkout_id' => $this->checkout->id,
            'status' => 'shipped',
            'message' => 'Your order has been shipped',
        ];
    }
}
