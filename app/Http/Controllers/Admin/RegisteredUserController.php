<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RegisteredUserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $provider = $request->query('provider');

        $users = User::query()
            ->withCount('checkouts')
            ->withSum(['checkouts as paid_total' => fn($query) => $query->where('status', 'Paid')], 'total_price')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                });
            })
            ->when($provider, function ($query) use ($provider) {
                if ($provider === 'email') {
                    $query->where(function ($sub) {
                        $sub->whereNull('auth_provider')->orWhere('auth_provider', '');
                    });
                } else {
                    $query->where('auth_provider', $provider);
                }
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->appends($request->query());

        $stats = [
            'total' => User::count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'google' => User::where('auth_provider', 'google')->count(),
            'facebook' => User::where('auth_provider', 'facebook')->count(),
        ];

        return view('admins.users.index', compact('users', 'stats', 'search', 'provider'));
    }
}
