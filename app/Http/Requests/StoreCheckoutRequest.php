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
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'coupon_code' => 'nullable|string|max:50|exists:coupons,code',
            'shipping_method' => 'required|in:standard,express,overnight',
            'payment_method' => 'required|in:card,bank,paypal,crypto',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'address.required' => 'Please provide a delivery address',
            'city.required' => 'Please select a city',
            'postal_code.required' => 'Postal code is required',
            'country.required' => 'Please select a country',
            'phone.required' => 'Phone number is required',
            'shipping_method.required' => 'Please select a shipping method',
            'payment_method.required' => 'Please select a payment method',
        ];
    }
}
