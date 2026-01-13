<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Checkout;

class UserDashboardController extends Controller
{
    public function dashboard()
    {
        return view('dashboard');
    }

    public function orders()
    {
        $orders = Checkout::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('dashboard.orders', compact('orders'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('dashboard.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|confirmed|min:8',
        ]);

        $user->name = $data['name'];
        $user->phone_number = $data['phone_number'] ?? $user->phone_number;
        $user->address = $data['address'] ?? $user->address;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('success', __('messages.profile.updated') ?? 'Profile updated successfully');
    }
}
