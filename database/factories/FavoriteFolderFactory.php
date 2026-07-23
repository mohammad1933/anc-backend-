<?php

namespace Database\Factories;

use App\Models\FavoriteFolder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FavoriteFolder>
 */
class FavoriteFolderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'icon' => 'folder',
            'sort_order' => fake()->numberBetween(1, 50),
        ];
    }
}
