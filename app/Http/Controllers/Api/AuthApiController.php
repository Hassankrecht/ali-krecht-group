<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WelcomeCouponAssigner;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthApiController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        app(WelcomeCouponAssigner::class)->assign($user->id);

        return response()->json([
            'message' => 'Registered successfully',
            'data' => $this->authPayload($user),
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        app(WelcomeCouponAssigner::class)->assign($user->id);

        return response()->json([
            'message' => 'Logged in successfully',
            'data' => $this->authPayload($user),
        ]);
    }

    public function googleLogin(Request $request)
    {
        $validated = $request->validate([
            'id_token' => ['required', 'string'],
        ]);

        $payload = $this->verifyGoogleIdToken($validated['id_token']);
        $email = isset($payload['email']) ? strtolower(trim($payload['email'])) : null;

        if (!$email || empty($payload['email_verified'])) {
            throw ValidationException::withMessages([
                'id_token' => ['Google email is not verified.'],
            ]);
        }

        $user = User::where('google_id', $payload['sub'])->first()
            ?: User::where('email', $email)->first();

        $isNewUser = !$user;

        if (!$user) {
            $user = new User();
            $user->email = $email;
            $user->password = Hash::make(Str::random(40));
        }

        $user->name = $user->name ?: ($payload['name'] ?? strstr($email, '@', true));
        $user->google_id = $payload['sub'];
        $user->auth_provider = 'google';
        $user->avatar = $payload['picture'] ?? $user->avatar;
        $user->email_verified_at = $user->email_verified_at ?: now();
        try {
            $user->save();
        } catch (QueryException $e) {
            if ($e->getCode() !== '23000') {
                throw $e;
            }

            $user = User::where('email', $email)->firstOrFail();
            $user->google_id = $payload['sub'];
            $user->auth_provider = 'google';
            $user->avatar = $payload['picture'] ?? $user->avatar;
            $user->email_verified_at = $user->email_verified_at ?: now();
            $user->save();
            $isNewUser = false;
        }

        app(WelcomeCouponAssigner::class)->assign($user->id);

        return response()->json([
            'message' => $isNewUser ? 'Registered with Google successfully' : 'Logged in with Google successfully',
            'data' => $this->authPayload($user),
        ], $isNewUser ? 201 : 200);
    }

    public function facebookLogin(Request $request)
    {
        $validated = $request->validate([
            'access_token' => ['required', 'string'],
        ]);

        $payload = $this->verifyFacebookAccessToken($validated['access_token']);
        $email = isset($payload['email']) ? strtolower(trim($payload['email'])) : null;

        if (!$email) {
            throw ValidationException::withMessages([
                'access_token' => ['Facebook account email is required.'],
            ]);
        }

        $user = User::where('facebook_id', $payload['id'])->first()
            ?: User::where('email', $email)->first();

        $isNewUser = !$user;

        if (!$user) {
            $user = new User();
            $user->email = $email;
            $user->password = Hash::make(Str::random(40));
        }

        $user->name = $user->name ?: ($payload['name'] ?? strstr($email, '@', true));
        $user->facebook_id = $payload['id'];
        $user->auth_provider = 'facebook';
        $user->avatar = $payload['picture']['data']['url'] ?? $user->avatar;
        $user->email_verified_at = $user->email_verified_at ?: now();
        try {
            $user->save();
        } catch (QueryException $e) {
            if ($e->getCode() !== '23000') {
                throw $e;
            }

            $user = User::where('email', $email)->firstOrFail();
            $user->facebook_id = $payload['id'];
            $user->auth_provider = 'facebook';
            $user->avatar = $payload['picture']['data']['url'] ?? $user->avatar;
            $user->email_verified_at = $user->email_verified_at ?: now();
            $user->save();
            $isNewUser = false;
        }

        app(WelcomeCouponAssigner::class)->assign($user->id);

        return response()->json([
            'message' => $isNewUser ? 'Registered with Facebook successfully' : 'Logged in with Facebook successfully',
            'data' => $this->authPayload($user),
        ], $isNewUser ? 201 : 200);
    }

    public function logout(Request $request)
    {
        $accessToken = $request->user()?->currentAccessToken();

        if ($accessToken instanceof PersonalAccessToken) {
            $accessToken->delete();
        }

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(['email' => $validated['email']]);

        return response()->json([
            'message' => __($status),
            'success' => $status === Password::RESET_LINK_SENT,
        ], $status === Password::RESET_LINK_SENT ? 200 : 422);
    }

    private function verifyGoogleIdToken(string $idToken): array
    {
        $clientIds = array_values(array_filter(array_map(
            fn ($id) => trim($id),
            explode(',', (string) config('auth.google_client_ids'))
        )));

        if (empty($clientIds)) {
            throw ValidationException::withMessages([
                'id_token' => ['Google login is not configured on the server.'],
            ]);
        }

        $response = Http::timeout(10)->get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

        if (!$response->ok()) {
            throw ValidationException::withMessages([
                'id_token' => ['Invalid Google token.'],
            ]);
        }

        $payload = $response->json();

        if (!in_array($payload['aud'] ?? null, $clientIds, true)) {
            throw ValidationException::withMessages([
                'id_token' => ['Google token audience is not allowed.'],
            ]);
        }

        if (($payload['iss'] ?? null) && !in_array($payload['iss'], ['accounts.google.com', 'https://accounts.google.com'], true)) {
            throw ValidationException::withMessages([
                'id_token' => ['Invalid Google token issuer.'],
            ]);
        }

        return $payload;
    }

    private function verifyFacebookAccessToken(string $accessToken): array
    {
        $appId = config('auth.facebook_app_id');
        $appSecret = config('auth.facebook_app_secret');

        if (!$appId || !$appSecret) {
            throw ValidationException::withMessages([
                'access_token' => ['Facebook login is not configured on the server.'],
            ]);
        }

        $debugResponse = Http::timeout(10)->get('https://graph.facebook.com/debug_token', [
            'input_token' => $accessToken,
            'access_token' => $appId . '|' . $appSecret,
        ]);

        if (!$debugResponse->ok()) {
            throw ValidationException::withMessages([
                'access_token' => ['Invalid Facebook token.'],
            ]);
        }

        $debugPayload = $debugResponse->json('data', []);

        if (empty($debugPayload['is_valid']) || ($debugPayload['app_id'] ?? null) !== $appId) {
            throw ValidationException::withMessages([
                'access_token' => ['Facebook token audience is not allowed.'],
            ]);
        }

        $profileResponse = Http::timeout(10)->get('https://graph.facebook.com/me', [
            'fields' => 'id,name,email,picture.type(large)',
            'access_token' => $accessToken,
        ]);

        if (!$profileResponse->ok()) {
            throw ValidationException::withMessages([
                'access_token' => ['Unable to read Facebook profile.'],
            ]);
        }

        return $profileResponse->json();
    }

    private function authPayload(User $user): array
    {
        $token = $user->createToken('flutter-app')->plainTextToken;

        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone_number,
                'avatar' => $user->avatar,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ];
    }
}


