<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductUpdated
{
    use Dispatchable, SerializesModels;

    public Product $product;
    public array $changes;

    /**
     * Create a new event instance.
     */
    public function __construct(Product $product, array $changes = [])
    {
        $this->product = $product;
        $this->changes = $changes;
    }
}
