<?php

namespace Database\Factories;

use App\Models\Court;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Court>
 */
class CourtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => 'Lapangan ' . Str::title($name),
            'slug' => Str::slug($name),
            'category' => 'futsal',
            'location' => 'Outdoor - Sintetis',
            'opens_at' => '07:00',
            'closes_at' => '21:00',
            'image' => null,
            'is_active' => true,
        ];
    }
}
