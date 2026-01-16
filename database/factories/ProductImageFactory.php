<?php

namespace Database\Factories;

use App\Models\ProductImage;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'image' => $this->faker->imageUrl(),
            'alt_text' => $this->faker->words(2, true),
            'order' => $this->faker->numberBetween(1, 5),
        ];
    }
}
