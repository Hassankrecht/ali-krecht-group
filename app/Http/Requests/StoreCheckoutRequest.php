<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isGuest = !auth()->check();

        return [
            'name' => $isGuest ? 'required|string|max:255' : 'nullable|string|max:255',
            'email' => $isGuest ? 'required|email|max:255' : 'nullable|email|max:255',
            'phone_number' => 'required|string|max:20',
            'town' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'zipcode' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'create_account' => 'nullable|boolean',
            'password' => 'nullable|string|min:6|confirmed',
            'g-recaptcha-response' => 'nullable|string',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please provide your name',
            'email.required' => 'Please provide your email address',
            'email.email' => 'Please provide a valid email address',
            'phone_number.required' => 'Phone number is required',
            'town.required' => 'Please provide your city',
            'country.required' => 'Please select a country',
            'zipcode.required' => 'Zip code is required',
            'address.required' => 'Please provide a delivery address',
            'password.confirmed' => 'Password confirmation does not match',
        ];
    }
}
