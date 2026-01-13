<?php

namespace App\Jobs;

use App\Models\Checkout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use PDF;

class GenerateOrderReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $startDate;
    public string $endDate;
    public int $maxRetries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $checkouts = Checkout::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->with(['user', 'items'])
            ->get();

        $stats = [
            'total_orders' => $checkouts->count(),
            'total_revenue' => $checkouts->sum('total_price'),
            'average_order_value' => $checkouts->avg('total_price'),
            'orders_by_status' => $checkouts->groupBy('status')->map->count(),
        ];

        // Generate PDF report
        $reportPath = storage_path('reports/order_report_' . date('Y-m-d_H-i-s') . '.pdf');

        // Create reports directory if not exists
        @mkdir(dirname($reportPath), 0755, true);

        // Here you would generate the PDF
        // For now, just store the JSON data
        file_put_contents($reportPath . '.json', json_encode($stats, JSON_PRETTY_PRINT));

        Log::info('Order report generated: ' . $reportPath);
    }
}
