<?php

namespace Database\Factories;

use App\Models\Favorite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Favorite>
 */
class FavoriteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'type' => fake()->randomElement(['collection', 'color', 'texture']),
            'collection' => fake()->randomElement(['Hotel Projects', 'Luxury Collection', 'Modern Fabrics', 'Curtains']),
            'material' => fake()->randomElement(['Silk Blend', 'Wool Mix', 'Linen Blend', 'Heavy Weight Polyester']),
            'image_url' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=900&q=85',
            'colors' => ['#896600', '#1B1C1C', '#E4E4E4'],
        ];
    }
}
