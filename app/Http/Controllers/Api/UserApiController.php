<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserApiController extends Controller
{
    public function show(Request $request)
    {
        return new UserResource($request->user());
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
                'errors' => [
                    'current_password' => ['Current password is incorrect.'],
                ],
            ], 422);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully',
            'data' => null,
        ]);
    }
    public function update(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|max:50',
            'phone_number' => 'sometimes|nullable|string|max:50',
            'country' => 'sometimes|nullable|string|max:100',
            'town' => 'sometimes|nullable|string|max:100',
            'city' => 'sometimes|nullable|string|max:100',
            'zipcode' => 'sometimes|nullable|string|max:20',
            'postal_code' => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string|max:500',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('phone') || $request->has('phone_number')) {
            $user->phone_number = $request->input('phone', $request->input('phone_number'));
        }
        if ($request->has('country')) {
            $user->country = $request->country;
        }
        if ($request->has('town') || $request->has('city')) {
            $user->town = $request->input('town', $request->input('city'));
        }
        if ($request->has('zipcode') || $request->has('postal_code')) {
            $user->zipcode = $request->input('zipcode', $request->input('postal_code'));
        }
        if ($request->has('address')) {
            $user->address = $request->address;
        }
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return new UserResource($user);
    }
}

