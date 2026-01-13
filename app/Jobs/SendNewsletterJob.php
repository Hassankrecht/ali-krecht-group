<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $subject;
    public string $content;
    public string $htmlContent;
    public int $maxRetries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(string $subject, string $content, string $htmlContent = '')
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->htmlContent = $htmlContent ?: $content;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = User::where('subscribed_to_newsletter', true)
            ->where('is_active', true)
            ->get();

        foreach ($users as $user) {
            try {
                Mail::to($user->email)
                    ->send(new \App\Mail\NewsletterMail(
                        $user,
                        $this->subject,
                        $this->content,
                        $this->htmlContent
                    ));
            } catch (\Exception $e) {
                Log::error('Newsletter send failed for user ' . $user->id . ': ' . $e->getMessage());
            }
        }

        Log::info('Newsletter sent to ' . $users->count() . ' users');
    }
}
