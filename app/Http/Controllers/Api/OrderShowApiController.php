<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Auth;

class OrderShowApiController extends Controller
{
    /**
     * Display a single order for the authenticated user.
     * @param int $id
     * @return OrderResource|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $order = Checkout::with(['items.product.translations', 'coupon'])
            ->where('user_id', $user->id)
            ->find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        return new OrderResource($order);
    }
}
