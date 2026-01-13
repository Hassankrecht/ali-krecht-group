<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DisableExpiredCoupons extends Command
{
    protected $signature = 'coupons:disable-expired';
    protected $description = 'Disable coupons that have passed their expiration date';

    public function handle(): int
    {
        $now = Carbon::now();
        $count = Coupon::where('status', true)
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<=', $now)
            ->update(['status' => false]);

        $this->info("Disabled {$count} expired coupons.");
        return Command::SUCCESS;
    }
}
