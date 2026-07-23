<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
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
            'name' => fake()->randomElement(['Apartment Styling', 'Office Reception', 'Restaurant Lounge', 'Villa Bedroom']),
            'client' => fake()->company(),
            'cover_image' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1200&q=85',
            'status' => fake()->randomElement(['active', 'in_review', 'completed']),
            'is_favorite' => false,
            'fabrics' => collect(range(1, fake()->numberBetween(4, 16)))->map(fn (int $number) => [
                'name' => "Fabric {$number}", 'collection' => 'Luxe Collection', 'color' => '#B78A1C',
            ])->all(),
            'saved_colors' => ['#B78A1C', '#18372D', '#E7DED0'],
            'notes' => ['Confirm final quantities with the client.', 'Review performance specifications.'],
            'members' => [['name' => 'Amelia Stone', 'role' => 'Lead Designer', 'initials' => 'AS']],
            'timeline' => [['title' => 'Concept approval', 'date' => now()->addWeek()->toDateString(), 'completed' => false]],
            'recent_activity' => [['text' => 'Project board updated', 'time' => 'Today']],
        ];
    }
}
