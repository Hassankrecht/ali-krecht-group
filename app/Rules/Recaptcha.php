<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Recaptcha implements Rule
{
    public function passes($attribute, $value): bool
    {
        $secret = env('RECAPTCHA_SECRET') ?: env('RECAPTCHA_SECRET_KEY');
        if (empty($secret) || empty($value)) {
            return false;
        }

        $response = @file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$value}"
        );

        if (!$response) {
            return false;
        }

        $result = json_decode($response, true);

        return isset($result['success']) && $result['success'] === true;
    }

    public function message(): string
    {
        return 'Please verify that you are not a robot.';
    }
}
