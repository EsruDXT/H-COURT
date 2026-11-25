<?php

use App\Models\Court;
use App\Models\User;

it('warns when reservation duration at 20:00 exceeds court closing time', function () {
    $court = Court::factory()->create();
    $date = now()->addDay()->toDateString();

    $this->actingAs(User::factory()->create())
        ->get(route('reservation.create', [
            'court' => $court->slug,
            'date' => $date,
            'time' => '20:00',
            'duration' => 2,
        ]))
        ->assertOk()
        ->assertSee('Durasi melewati jam operasional lapangan. Pilih jam atau durasi lain.')
        ->assertSee('data-reservation-time-form', false)
        ->assertSee('data-closing-time="21:00"', false)
        ->assertSee('data-duration-warning', false);
});

it('allows a one-hour reservation starting at 20:00 when court closes at 21:00', function () {
    $court = Court::factory()->create();
    $date = now()->addDay()->toDateString();

    $this->actingAs(User::factory()->create())
        ->get(route('reservation.create', [
            'court' => $court->slug,
            'date' => $date,
            'time' => '20:00',
            'duration' => 1,
        ]))
        ->assertOk()
        ->assertSee('20:00 - 21:00');
});
