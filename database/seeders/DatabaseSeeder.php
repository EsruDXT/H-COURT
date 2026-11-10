<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\Court;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Pengelola',
            'email' => 'admin@hcourt.test',
            'role' => 'pengelola',
        ]);

        $student = User::factory()->create([
            'name' => 'Siswa Contoh',
            'email' => 'siswa@hcourt.test',
            'role' => 'siswa',
            'kelas' => 'XII TKJ 1',
        ]);

        $this->call(CourtSeeder::class);

        Reservation::factory()
            ->for($student)
            ->for(Court::where('slug', 'futsal')->firstOrFail())
            ->create(['status' => Reservation::STATUS_CONFIRMED]);
    }
}
