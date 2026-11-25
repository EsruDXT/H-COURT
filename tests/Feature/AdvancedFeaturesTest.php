
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
    $this->travelTo(
        Carbon::parse('2026-11-18 08:00:00', 'Asia/Pontianak')
    );
});

afterEach(function () {
    $this->travelBack();
});

it('sends a password reset notification to the registered email', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.email'), [
        'email' => $user->email,
    ])->assertSessionHas('status');

    Notification::assertSentTo($user, ResetPassword::class);
});

it('allows an owner to cancel their future reservation and notifies admins', function () {
    Notification::fake();

    $owner = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $court = Court::factory()->create();

    $reservation = Reservation::factory()
        ->for($owner)
        ->for($court)
        ->create([
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

    Notification::assertSentTo(
        $admin,
        ReservationActivityNotification::class
    );
});

it('records reviewer and review time when an admin approves a reservation', function () {
    Notification::fake();

    $owner = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $court = Court::factory()->create();

    $reservation = Reservation::factory()
        ->for($owner)
        ->for($court)
        ->create([
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

    Notification::assertSentTo(
        $owner,
        ReservationActivityNotification::class
    );
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
        ->put(
            route('admin.courts.operating-hours.update', $court),
            ['hours' => $hours]
        )
        ->assertSessionHas('status');

    $this->assertDatabaseHas('court_operating_hours', [
        'court_id' => $court->id,
        'day_of_week' => 0,
        'is_closed' => 1,
    ]);

    expect(
        CourtOperatingHour::query()
            ->where('court_id', $court->id)
            ->count()
    )->toBe(7);
});

it('shows the admin report page for admins', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.reports.index'))
        ->assertOk()
        ->assertSee('Laporan Statistik');
});

it('forbids non-admin users from the report page and export', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.reports.index'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('admin.reports.export'))
        ->assertForbidden();
});

it('rejects a report range where the start date is after the end date', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->from(route('admin.reports.index'))
        ->get(route('admin.reports.index', [
            'from' => '2026-11-20',
            'to' => '2026-11-01',
        ]))
        ->assertSessionHasErrors('to');
});

it('exports reservations as csv and neutralises spreadsheet formulas', function () {
    $admin = User::factory()->admin()->create();

    Reservation::factory()->create([
        'borrower_name' => '=HYPERLINK("http://evil.test")',
        'reservation_date' => '2026-11-19',
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.reports.export', [
            'from' => '2026-11-01',
            'to' => '2026-11-30',
        ]))
        ->assertOk()
        ->assertDownload(
            'laporan-reservasi-2026-11-01-2026-11-30.csv'
        );

    $content = $response->streamedContent();

    expect($content)
        ->toContain("'=HYPERLINK")
        ->not->toContain(',=HYPERLINK');
});

it('lists only the notifications of the logged-in user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $user->notify(
        new ReservationActivityNotification(
            'Judul milikku',
            'Pesan',
            '/reservation'
        )
    );

    $other->notify(
        new ReservationActivityNotification(
            'Judul orang lain',
            'Pesan',
            '/reservation'
        )
    );

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Judul milikku')
        ->assertDontSee('Judul orang lain');
});

it('marks a notification as read and can open its target page', function () {
    $user = User::factory()->create();

    $user->notify(
        new ReservationActivityNotification(
            'Judul',
            'Pesan',
            '/reservation'
        )
    );

    $notification = $user->notifications()->first();

    expect($notification)->not->toBeNull();

    $this->actingAs($user)
        ->patch(route('notifications.read', $notification->id), [
            'open' => 1,
        ])
        ->assertRedirect('/reservation');

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('does not let a user mark another user\'s notification as read', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    $owner->notify(
        new ReservationActivityNotification(
            'Judul',
            'Pesan',
            '/reservation'
        )
    );

    $notification = $owner->notifications()->first();

    expect($notification)->not->toBeNull();

    $this->actingAs($intruder)
        ->patch(route('notifications.read', $notification->id))
        ->assertNotFound();

    expect($notification->fresh()->read_at)->toBeNull();
});

it('marks all notifications as read', function () {
    $user = User::factory()->create();

    $user->notify(
        new ReservationActivityNotification('A', 'Pesan', '/reservation')
    );

    $user->notify(
        new ReservationActivityNotification('B', 'Pesan', '/reservation')
    );

    $this->actingAs($user)
        ->post(route('notifications.read-all'))
        ->assertSessionHas('status');

    expect($user->unreadNotifications()->count())->toBe(0);
});
