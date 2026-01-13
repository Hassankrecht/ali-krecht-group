<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Recaptcha;

class StoreReviewRequest extends FormRequest
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
            'name'       => 'required|string|max:255',
            'profession' => 'nullable|string|max:255',
            'rating'     => 'required|integer|min:1|max:5',
            'review'     => 'required|string|max:1000',
            'photo'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'g-recaptcha-response' => $recaptchaRule,
        ];
    }
}
