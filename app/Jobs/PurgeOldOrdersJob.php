<?php

namespace App\Jobs;

use App\Models\Checkout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurgeOldOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $daysOld;
    public int $maxRetries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(int $daysOld = 180)
    {
        $this->daysOld = $daysOld;
    }

    /**
     * Execute the job - archive and delete old orders
     */
    public function handle(): void
    {
        $cutoffDate = now()->subDays($this->daysOld);

        try {
            DB::transaction(function () use ($cutoffDate) {
                // Get old orders before deleting
                $oldCheckouts = Checkout::where('created_at', '<=', $cutoffDate)
                    ->where('status', 'delivered')
                    ->get();

                // Log them to archive (optional)
                foreach ($oldCheckouts as $checkout) {
                    Log::info('Archiving order: ' . $checkout->id);
                }

                // Delete old checkout items
                DB::table('checkout_items')
                    ->whereIn('checkout_id', $oldCheckouts->pluck('id'))
                    ->delete();

                // Delete old checkouts
                Checkout::where('created_at', '<=', $cutoffDate)
                    ->where('status', 'delivered')
                    ->delete();

                Log::info('Purged ' . $oldCheckouts->count() . ' old orders');
            });
        } catch (\Exception $e) {
            Log::error('Order purge failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
