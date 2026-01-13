<?php

namespace App\Events;

use App\Models\Checkout;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmed
{
    use Dispatchable, SerializesModels;

    public Checkout $checkout;

    /**
     * Create a new event instance.
     */
    public function __construct(Checkout $checkout)
    {
        $this->checkout = $checkout;
    }
}
