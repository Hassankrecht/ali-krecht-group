<?php

namespace Database\Factories;

use App\Models\CheckoutItem;
use App\Models\Checkout;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class CheckoutItemFactory extends Factory
{
    protected $model = CheckoutItem::class;

    public function definition(): array
    {
        return [
            'checkout_id' => Checkout::factory(),
            'product_id' => Product::factory(),
            'name' => $this->faker->words(2, true),
            'image' => $this->faker->imageUrl(),
            'quantity' => $this->faker->numberBetween(1, 5),
            'price' => $this->faker->randomFloat(2, 10, 200),
            'total_price' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}
