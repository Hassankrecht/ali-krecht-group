<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateProductStockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $productId;
    public int $quantity;
    public string $action; // 'add' or 'subtract'
    public int $maxRetries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(int $productId, int $quantity, string $action = 'subtract')
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->action = $action;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $product = Product::find($this->productId);

        if (!$product) {
            Log::warning('Product not found: ' . $this->productId);

            return;
        }

        if ($this->action === 'add') {
            $product->increment('stock', $this->quantity);
        } elseif ($this->action === 'subtract') {
            if ($product->stock >= $this->quantity) {
                $product->decrement('stock', $this->quantity);
            } else {
                Log::warning('Insufficient stock for product: ' . $this->productId);
            }
        }

        Log::info('Product stock updated: ' . $this->productId . ' (' . $this->action . ' ' . $this->quantity . ')');
    }
}
