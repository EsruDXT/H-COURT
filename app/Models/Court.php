<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\CourtFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'category', 'location', 'opens_at', 'closes_at', 'image', 'is_active'])]
class Court extends Model
{
    /** @use HasFactory<CourtFactory> */
    use HasFactory;

    public const CATEGORIES = [
        'futsal' => 'Futsal',
        'basket' => 'Basket',
        'badminton' => 'Badminton',
        'lainnya' => 'Lainnya',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function operatingHours(): HasMany
    {
        return $this->hasMany(CourtOperatingHour::class);
    }

    public function closures(): HasMany
    {
        return $this->hasMany(CourtClosure::class);
    }

    public function imageUrl(): string
    {
        return asset($this->image ?: 'images/courts/'.$this->category.'.jpg');
    }

    /** Returns the opening window for a date, or null when the court is closed. */
    public function operatingWindowForDate(string $date): ?array
    {
        $date = CarbonImmutable::parse($date);

        if ($this->closures()->whereDate('closure_date', $date->toDateString())->exists()) {
            return null;
        }

        $weekly = $this->operatingHours()
            ->where('day_of_week', $date->dayOfWeek)
            ->first();

        if ($weekly?->is_closed) {
            return null;
        }

        $opensAt = substr((string) ($weekly?->opens_at ?? $this->opens_at), 0, 5);
        $closesAt = substr((string) ($weekly?->closes_at ?? $this->closes_at), 0, 5);

        if ($opensAt === '' || $closesAt === '' || $closesAt <= $opensAt) {
            return null;
        }

        return ['opens_at' => $opensAt, 'closes_at' => $closesAt];
    }

    /** Returns valid one-hour start slots for the selected date. */
    public function timeSlotsForDate(string $date): array
    {
        $window = $this->operatingWindowForDate($date);

        if ($window === null) {
            return [];
        }

        $cursor = CarbonImmutable::createFromFormat('H:i', $window['opens_at']);
        $closing = CarbonImmutable::createFromFormat('H:i', $window['closes_at']);
        $slots = [];

        while ($cursor->addHour()->lessThanOrEqualTo($closing)) {
            $slots[] = $cursor->format('H:i');
            $cursor = $cursor->addHour();
        }

        return $slots;
    }

    /** @return array<string, string> */
    public function slotAvailability(string $date): array
    {
        $slots = $this->timeSlotsForDate($date);

        if ($slots === []) {
            return [];
        }

        $reservations = $this->reservations()
            ->whereDate('reservation_date', $date)
            ->whereIn('status', Reservation::BLOCKING_STATUSES)
            ->get();

        $today = now()->toDateString();
        $nowHm = now()->format('H:i');
        $result = [];

        foreach ($slots as $slot) {
            $matching = $reservations->filter(fn (Reservation $reservation): bool => $reservation->coversSlot($slot));

            if ($matching->contains('status', Reservation::STATUS_CONFIRMED)) {
                $result[$slot] = 'booked';
            } elseif ($matching->isNotEmpty()) {
                $result[$slot] = 'pending';
            } elseif ($date < $today || ($date === $today && $slot <= $nowHm)) {
                $result[$slot] = 'past';
            } else {
                $result[$slot] = 'available';
            }
        }

        return $result;
    }

    /**
     * Must be called inside a transaction after locking this Court row.
     * Locking the parent court serializes two concurrent booking attempts even
     * when no reservation row exists yet for the selected slot.
     */
    public function hasConflict(string $date, string $start, string $end, ?int $exceptId = null): bool
    {
        return $this->reservations()
            ->whereDate('reservation_date', $date)
            ->whereIn('status', Reservation::BLOCKING_STATUSES)
            ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
            ->lockForUpdate()
            ->get()
            ->contains(fn (Reservation $reservation): bool => $reservation->startHm() < $end && $reservation->endHm() > $start);
    }
}
