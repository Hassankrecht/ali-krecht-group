<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactFormAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $name;
    public string $email;
    public string $subject;
    public string $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $name, string $email, string $subject, string $message)
    {
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
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
            ->subject('New Contact Form Submission: ' . $this->subject)
            ->greeting('Hello Admin,')
            ->line('You have received a new contact form submission.')
            ->line('**From:** ' . $this->name)
            ->line('**Email:** ' . $this->email)
            ->line('**Subject:** ' . $this->subject)
            ->line('**Message:**')
            ->line($this->message)
            ->action('Reply to Message', url('/admin/messages'))
            ->line('Please respond to this inquiry at your earliest convenience.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'from' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => substr($this->message, 0, 100) . '...',
        ];
    }
}
