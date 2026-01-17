<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserService>
 */
class UserServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'user_id' => \App\Models\User::factory(),
            'service_id' => \App\Models\Service::factory(),
            'phone_numbers' => json_encode([fake()->phoneNumber()]),
            'email' => fake()->safeEmail(),
            'opening_time' => '08:00:00',
            'closing_time' => '18:00:00',
            'all_day' => false,
            'min_price' => fake()->randomFloat(2, 100, 1000),
            'max_price' => fake()->randomFloat(2, 1000, 10000),
            'ratings' => fake()->numberBetween(1, 5),
            'country_id' => null,
            'state_id' => null,
            'location_id' => null,
            'address' => fake()->address(),
            'longitude' => fake()->longitude(),
            'latitude' => fake()->latitude(),
            'description' => fake()->paragraph(),
            'verified' => true,
            'suspended' => false,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verified' => false,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'suspended' => true,
        ]);
    }

    public function allDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'all_day' => true,
            'opening_time' => null,
            'closing_time' => null,
        ]);
    }
}
