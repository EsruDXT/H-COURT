<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Court;
use App\Models\Reservation;
use App\Models\User;

class CourtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courts = [
            ['name' => 'Lapangan Futsal', 'slug' => 'futsal', 'category' => 'futsal', 'location' => 'Outdoor - Sintetis', 'image' => 'images/courts/futsal.jpg'],
            ['name' => 'Lapangan Basket', 'slug' => 'basket', 'category' => 'basket', 'location' => 'Indoor - gedung olahraga', 'image' => 'images/courts/basket.jpg'],
            ['name' => 'Lapangan Badminton', 'slug' => 'badminton', 'category' => 'badminton', 'location' => 'Indoor - gedung olahraga', 'image' => 'images/courts/badminton.jpg'],
        ];

        foreach ($courts as $court) {
            Court::updateOrCreate(['slug' => $court['slug']], $court);
        }
    }
}
