<?php

use App\Models\User;

it('uses the user layout for user notifications', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Jadwal Lapangan')
        ->assertDontSee('Kelola Lapangan')
        ->assertSee('mx-auto max-w-6xl px-4 py-10', false);
});

it('preserves the admin layout for admin notifications', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Kelola Lapangan')
        ->assertDontSee('Jadwal Lapangan');
});
