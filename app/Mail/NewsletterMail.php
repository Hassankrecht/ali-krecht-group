<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $subject;
    public string $content;
    public string $htmlContent;

    public function __construct(User $user, string $subject, string $content, string $htmlContent = '')
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->content = $content;
        $this->htmlContent = $htmlContent ?: $content;
    }

    public function build()
    {
        return $this
            ->subject($this->subject)
            ->view('emails.newsletter')
            ->with([
                'user' => $this->user,
                'subject' => $this->subject,
                'content' => $this->content,
                'htmlContent' => $this->htmlContent,
            ]);
    }
}
