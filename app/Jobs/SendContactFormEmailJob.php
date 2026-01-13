<?php

namespace App\Jobs;

use App\Mail\ContactFormMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendContactFormEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $name;
    public string $email;
    public string $phone;
    public string $subject;
    public string $message;

    /**
     * Create a new job instance.
     */
    public function __construct(string $name, string $email, string $phone, string $subject, string $message)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Send to admin
        $adminEmail = config('mail.admin_address', 'admin@alikrecht.com');
        Mail::to($adminEmail)->send(new ContactFormMail(
            $this->name,
            $this->email,
            $this->phone,
            $this->subject,
            $this->message
        ));

        // Send confirmation to user
        Mail::to($this->email)->send(new ContactFormMail(
            $this->name,
            $this->email,
            $this->phone,
            $this->subject,
            $this->message,
            true // isConfirmation flag
        ));
    }
}
