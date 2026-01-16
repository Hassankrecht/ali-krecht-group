<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;
use App\Rules\Recaptcha;

class ContactController extends Controller
{
    public function send(ContactFormRequest $request)
    {
        // Validation is now handled by ContactFormRequest

        // Save in database
        Contact::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        // Send email
        Mail::to('you@example.com')->send(new ContactFormMail($request->all()));

        return redirect()->back()->with('success', 'Message sent successfully!');
    }
}
