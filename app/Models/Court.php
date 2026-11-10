<?php

namespace App\Models;

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

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function imageUrl(): string
    {
        return asset($this->image ?: 'images/courts/'.$this->category.'.jpg');
    }

    /**
     * Status tiap slot jam pada tanggal tertentu:
     * available | pending | booked | past.
     *
     * @return array<string, string>
     */
    public function slotAvailability(string $date): array
    {
        $reservations = $this->reservations()
            ->whereDate('reservation_date', $date)
            ->whereIn('status', Reservation::BLOCKING_STATUSES)
            ->get();

        $today = now()->toDateString();
        $nowHm = now()->format('H:i');
        $result = [];

        foreach (Reservation::TIME_SLOTS as $slot) {
            $matching = $reservations->filter(fn (Reservation $r): bool => $r->coversSlot($slot));

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
     * Jam mulai yang tidak bisa dipilih pada tanggal tertentu.
     *
     * @return array<int, string>
     */
    public function bookedStartTimes(string $date): array
    {
        return array_keys(array_filter(
            $this->slotAvailability($date),
            fn (string $status): bool => $status !== 'available',
        ));
    }

    /**
     * Cek bentrok jadwal ($start dan $end berformat H:i). Gunakan di dalam transaksi.
     */
    public function hasConflict(string $date, string $start, string $end, ?int $exceptId = null): bool
    {
        return $this->reservations()
            ->whereDate('reservation_date', $date)
            ->whereIn('status', Reservation::BLOCKING_STATUSES)
            ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
            ->lockForUpdate()
            ->get()
            ->contains(fn (Reservation $r): bool => $r->startHm() < $end && $r->endHm() > $start);
    }
}
