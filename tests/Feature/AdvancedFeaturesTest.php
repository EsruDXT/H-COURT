<?php

use App\Models\Court;
use App\Models\CourtOperatingHour;
use App\Models\Reservation;
use App\Models\User;
use App\Notifications\ReservationActivityNotification;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
    $this->travelTo(Carbon::parse('2026-11-18 08:00:00', 'Asia/Pontianak'));
});

afterEach(function () {
    $this->travelBack();
});

it('sends a password reset notification to the registered email', function () {
    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email])
        ->assertSessionHas('status');

    Notification::assertSentTo($user, ResetPassword::class);
});

it('allows an owner to cancel their future reservation and notifies admins', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $court = Court::factory()->create();
    $reservation = Reservation::factory()->for($owner)->for($court)->create([
        'reservation_date' => '2026-11-19',
        'start_time' => '13:00',
        'end_time' => '14:00',
        'status' => Reservation::STATUS_CONFIRMED,
    ]);

    $this->actingAs($owner)
        ->patch(route('reservation.cancel', $reservation))
        ->assertRedirect(route('reservation.index'));

    $this->assertDatabaseHas('reservations', [
        'id' => $reservation->id,
        'status' => Reservation::STATUS_CANCELLED,
        'cancelled_by' => $owner->id,
    ]);

    Notification::assertSentTo($admin, ReservationActivityNotification::class);
});

it('records reviewer and review time when an admin approves a reservation', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $court = Court::factory()->create();
    $reservation = Reservation::factory()->for($owner)->for($court)->create([
        'reservation_date' => '2026-11-19',
        'start_time' => '13:00',
        'end_time' => '14:00',
        'status' => Reservation::STATUS_PENDING,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.reservations.approve', $reservation))
        ->assertSessionHas('status');

    $this->assertDatabaseHas('reservations', [
        'id' => $reservation->id,
        'status' => Reservation::STATUS_CONFIRMED,
        'reviewed_by' => $admin->id,
    ]);

    expect($reservation->fresh()->reviewed_at)->not->toBeNull();
    Notification::assertSentTo($owner, ReservationActivityNotification::class);
});

it('saves weekly operating hours for a court', function () {
    $admin = User::factory()->admin()->create();
    $court = Court::factory()->create();
    $hours = [];

    foreach (range(0, 6) as $day) {
        $hours[$day] = [
            'is_closed' => $day === 0 ? 1 : 0,
            'opens_at' => $day === 0 ? null : '08:00',
            'closes_at' => $day === 0 ? null : '20:00',
        ];
    }

    $this->actingAs($admin)
        ->put(route('admin.courts.operating-hours.update', $court), ['hours' => $hours])
        ->assertSessionHas('status');

    $this->assertDatabaseHas('court_operating_hours', [
        'court_id' => $court->id,
        'day_of_week' => 0,
        'is_closed' => 1,
    ]);

    expect(CourtOperatingHour::query()->where('court_id', $court->id)->count())->toBe(7);
});

it('shows the admin report page for admins', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.reports.index'))
        ->assertOk()
        ->assertSee('Laporan Statistik');
});
