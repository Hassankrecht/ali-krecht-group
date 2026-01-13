<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contact;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'subject' => fake()->words(3, true),
            'message' => fake()->paragraph(),
            'country' => fake()->country(),
            'city' => fake()->city(),
            'company' => fake()->company(),
            'type' => fake()->randomElement(['general', 'support', 'sales', 'partnership', 'complaint']),
            'status' => 'new',
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'assigned_to' => null,
            'notes' => fake()->optional()->sentence(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'is_read' => false,
            'replied_at' => null,
        ];
    }

    /**
     * Indicate that the contact has been read
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
        ]);
    }

    /**
     * Indicate that the contact has been replied
     */
    public function replied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'replied',
            'replied_at' => now(),
        ]);
    }

    /**
     * Indicate that the contact is spam
     */
    public function spam(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'spam',
            'priority' => 'low',
        ]);
    }

    /**
     * Indicate that the contact is closed
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
        ]);
    }

    /**
     * Indicate that the contact is a support request
     */
    public function supportRequest(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'support',
            'priority' => 'normal',
        ]);
    }

    /**
     * Indicate that the contact is a sales inquiry
     */
    public function salesInquiry(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'sales',
            'priority' => 'normal',
        ]);
    }

    /**
     * Indicate that the contact is a partnership request
     */
    public function partnershipRequest(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'partnership',
            'priority' => 'high',
        ]);
    }

    /**
     * Indicate that the contact is assigned to an admin
     */
    public function assignedTo(int $adminId): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_to' => $adminId,
        ]);
    }

    /**
     * Indicate that the contact has high priority
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }

    /**
     * Indicate that the contact has urgent priority
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
            'status' => 'new',
        ]);
    }
}
