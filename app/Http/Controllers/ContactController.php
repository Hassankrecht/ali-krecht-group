<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'name'    => 'required',
            'email'   => 'required|email',
            'subject' => 'required',
            'message' => 'required',
            'g-recaptcha-response' => 'required', // ⬅️ التحقق من reCAPTCHA
        ]);

        // ========= GOOGLE RECAPTCHA VERIFY =========
        $recaptchaSecret = env('RECAPTCHA_SECRET');

        $response = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$request->input('g-recaptcha-response')}"
        );

        $responseKeys = json_decode($response, true);

        if (!$responseKeys["success"]) {
            return back()->withErrors(['captcha' => 'Please verify that you are not a robot.']);
        }
        // ============================================

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
