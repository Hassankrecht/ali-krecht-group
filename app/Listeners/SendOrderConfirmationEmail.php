<?php

namespace App\Listeners;

use App\Events\OrderConfirmed;
use App\Jobs\OrderConfirmationJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderConfirmationEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event - dispatch email job
     * Note: OrderConfirmationJob is already dispatched in CheckoutController
     * This listener can be used for additional order confirmation actions
     */
    public function handle(OrderConfirmed $event): void
    {
        // Email is already sent by OrderConfirmationJob
        // This can be extended for additional notifications

        // Example: Update inventory, send SMS, etc.
        // OrderConfirmationJob::dispatch($event->checkout->id);
    }
}
