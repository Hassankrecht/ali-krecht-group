<?php

namespace App\Notifications;

use App\Models\Checkout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmationNotification extends Notification implements ShouldQueue
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
            ->subject('Order Confirmation #' . $this->checkout->id)
            ->greeting('Hello ' . $this->checkout->name . ',')
            ->line('Thank you for your order!')
            ->line('Order ID: ' . $this->checkout->id)
            ->line('Total Amount: ' . number_format($this->checkout->total_price, 2))
            ->line('Status: ' . ucfirst($this->checkout->status))
            ->action('View Order', url('/orders/' . $this->checkout->id))
            ->line('Thank you for shopping with us!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'checkout_id' => $this->checkout->id,
            'total_price' => $this->checkout->total_price,
            'status' => $this->checkout->status,
        ];
    }
}
