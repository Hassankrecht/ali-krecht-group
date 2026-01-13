<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->paragraph(),
            'long_description' => fake()->paragraphs(3, true),
            'image' => fake()->imageUrl(400, 300, 'business', true),
            'category' => fake()->randomElement(['web', 'mobile', 'design', 'consulting']),
            'client_name' => fake()->company(),
            'client_url' => fake()->url(),
            'project_url' => fake()->url(),
            'completion_date' => fake()->dateTime(),
            'technologies' => fake()->words(5),
            'features' => fake()->sentences(5),
            'status' => 'completed',
            'is_featured' => fake()->boolean(),
            'views' => fake()->numberBetween(0, 10000),
            'order' => fake()->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the project is featured
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the project is in progress
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'completion_date' => null,
        ]);
    }

    /**
     * Indicate that the project is on hold
     */
    public function onHold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'on_hold',
        ]);
    }

    /**
     * Indicate that the project is a web project
     */
    public function webProject(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'web',
        ]);
    }

    /**
     * Indicate that the project is a mobile project
     */
    public function mobileProject(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'mobile',
        ]);
    }

    /**
     * Indicate that the project is a design project
     */
    public function designProject(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'design',
        ]);
    }

    /**
     * Indicate that the project is popular
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'views' => fake()->numberBetween(1000, 50000),
        ]);
    }
}
