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

    /**
     * Jam mulai (format H:i) yang sudah terisi pada tanggal tertentu.
     *
     * @return array<int, string>
     */
    public function bookedStartTimes(string $date): array
    {
        return $this->reservations()
            ->whereDate('reservation_date', $date)
            ->whereIn('status', Reservation::BLOCKING_STATUSES)
            ->pluck('start_time')
            ->map(fn (string $time): string => substr($time, 0, 5))
            ->all();
    }
}