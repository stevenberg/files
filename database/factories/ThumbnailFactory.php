<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Thumbnail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Thumbnail>
 */
class ThumbnailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path_template' => fake()->unique()->word().'_[SIZE].png',
            'width' => 1,
            'height' => 1,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Thumbnail $thumbnail) {
            $thumbnail->paths()->each(function ($path) {
                copy(
                    base_path('tests/fixtures/test.png'),
                    Storage::path($path),
                );
            });
        });
    }
}
