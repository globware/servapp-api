<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => fake()->imageUrl(),
            'disk' => 'public',
            'path' => 'uploads/' . fake()->word(),
            'file_type' => 'image',
            'mime_type' => 'image/jpeg',
            'filename' => fake()->word() . '.jpg',
            'original_filename' => fake()->word() . '.jpg',
            'extension' => 'jpg',
            'size' => fake()->numberBetween(1000, 1000000),
            'formatted_size' => '1 MB',
        ];
    }

    public function image(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_type' => 'image',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
        ]);
    }

    public function video(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_type' => 'video',
            'mime_type' => 'video/mp4',
            'extension' => 'mp4',
        ]);
    }
}
