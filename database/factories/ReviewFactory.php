<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'profession' => $this->faker->jobTitle(),
            'rating' => $this->faker->numberBetween(1, 5),
            'photo' => null,
            'review' => $this->faker->paragraph(),
            'is_approved' => true,
        ];
    }
}
