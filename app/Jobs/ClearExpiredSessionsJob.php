<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClearExpiredSessionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job - clean up expired sessions
     */
    public function handle(): void
    {
        $expiresAt = now()->subMinutes(config('session.lifetime', 120));

        // Delete expired sessions from database
        DB::table('sessions')
            ->where('last_activity', '<=', $expiresAt->timestamp)
            ->delete();

        // Delete expired password reset tokens
        DB::table('password_reset_tokens')
            ->where('created_at', '<=', now()->subHours(1))
            ->delete();

        // Delete expired tokens
        DB::table('personal_access_tokens')
            ->where('created_at', '<=', now()->subDays(30))
            ->where('revoked', true)
            ->delete();

        Log::info('Expired sessions cleared successfully');
    }
}
