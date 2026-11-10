<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Court;
use App\Models\User;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'court_id' => Court::factory(),
            'reservation_date' => now()->addDay()->toDateString(),
            'start_time' => '15:00',
            'end_time' => '16:00',
            'borrower_name' => fake()->name(),
            'class_institution' => 'XII TKJ 1',
            'phone_number' => fake()->numerify('08##########'),
            'participant_count' => 5,
            'purpose' => 'Latihan rutin',
            'notes' => null,
            'status' => Reservation::STATUS_PENDING,
        ];
    }
}
