<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Folder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Folder>
 */
class FolderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => Str::title(fake()->unique()->word()),
        ];
    }

    public function inRoot(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'folder_id' => Folder::root()->firstOrFail()->id,
            ];
        });
    }
}
