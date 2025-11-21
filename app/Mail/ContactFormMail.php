<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->from('h.krecht01@gmail.com', 'Restoran Contact')
                    ->subject('New Contact Message')
                    ->view('emails.contact')  // تأكد أن هذا الملف موجود في resources/views/emails/contact.blade.php
                    ->with('data', $this->data);
    }


}