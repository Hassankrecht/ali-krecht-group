<?php

namespace App\Jobs;

use App\Mail\OrderPlacedMail;
use App\Models\Checkout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class OrderConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $checkoutId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $checkoutId)
    {
        $this->checkoutId = $checkoutId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $checkout = Checkout::with(['items', 'user'])->find($this->checkoutId);

        if (!$checkout) {
            return;
        }

        // Recipient: customer (fallback to no-reply)
        $customerEmail = $checkout->email ?? config('mail.from.address', 'no-reply@alikrecht.com');
        Mail::to($customerEmail)->send(new OrderPlacedMail($checkout, false));

        // Recipient: admin/ops
        $adminEmail = config('mail.admin_address', 'alikrecht.admin@gmail.com');
        Mail::to($adminEmail)->send(new OrderPlacedMail($checkout, true));
    }
}
