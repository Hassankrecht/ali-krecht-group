<?php

namespace Database\Factories;

use App\Models\Checkout;
use App\Models\User;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Checkout>
 */
class CheckoutFactory extends Factory
{
    protected $model = Checkout::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalBeforeDiscount = fake()->randomFloat(2, 100, 10000);
        $discount = fake()->randomFloat(2, 0, $totalBeforeDiscount * 0.5);

        return [
            'user_id' => User::factory(),
            'coupon_id' => fake()->optional(0.3)->randomElement(Coupon::pluck('id')->toArray()),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'town' => fake()->city(),
            'country' => fake()->country(),
            'zipcode' => fake()->postcode(),
            'address' => fake()->address(),
            'total_price' => $totalBeforeDiscount - $discount,
            'total_before_discount' => $totalBeforeDiscount,
            'discount_amount' => $discount,
            'status' => fake()->randomElement(['pending', 'paid', 'shipped', 'delivered']),
        ];
    }

    /**
     * Indicate that the checkout should be pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the checkout should be paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
        ]);
    }

    /**
     * Indicate that the checkout should be shipped.
     */
    public function shipped(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'shipped',
        ]);
    }
}
