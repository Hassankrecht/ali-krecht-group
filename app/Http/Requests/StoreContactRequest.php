<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Recaptcha;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $siteKey = env('RECAPTCHA_SITE_KEY');
        $secret = env('RECAPTCHA_SECRET') ?: env('RECAPTCHA_SECRET_KEY');
        $recaptchaRule = ($siteKey && $secret)
            ? ['required', new Recaptcha]
            : ['nullable'];

        return [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'g-recaptcha-response' => $recaptchaRule,
        ];
    }
}
