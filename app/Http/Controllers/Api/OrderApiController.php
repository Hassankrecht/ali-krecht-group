<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;

class OrderApiController extends Controller
{
    /**
     * Display a listing of the authenticated user's orders.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $orders = Checkout::with('items')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);
        return OrderResource::collection($orders);
    }
}
