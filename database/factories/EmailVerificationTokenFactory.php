<?php
// database/factories/EmailVerificationTokenFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EmailVerificationTokenFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => fake()->safeEmail(),
            'user_id' => null,
            'token_signature' => hash('sha256', fake()->sha256()),
            'expires_at' => now()->addHours(24),
            'verified' => false,
        ];
    }
}