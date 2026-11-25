<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    public const BLOCKING_STATUSES = [self::STATUS_PENDING, self::STATUS_CONFIRMED];

    /** Slot satu jam dipertahankan sebagai fallback kompatibilitas. Slot aktual dihitung dari Court. */
    public const TIME_SLOTS = ['13:00', '14:00', '15:00', '16:00', '17:00'];
    public const DAY_END = '18:00';
    public const MAX_DURATION = 3;

    protected $fillable = [
        'user_id', 'court_id', 'reservation_date', 'start_time', 'end_time',
        'borrower_name', 'class_institution', 'phone_number', 'participant_count',
        'purpose', 'notes', 'status', 'reviewed_at', 'reviewed_by',
        'cancelled_at', 'cancelled_by',
    ];

    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
            'participant_count' => 'integer',
            'reviewed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function startHm(): string
    {
        return substr((string) $this->start_time, 0, 5);
    }

    public function endHm(): string
    {
        return substr((string) $this->end_time, 0, 5);
    }

    public function timeRange(): string
    {
        return $this->startHm().' - '.$this->endHm();
    }

    public function durationInHours(): int
    {
        return (int) max(1, round((strtotime($this->endHm()) - strtotime($this->startHm())) / 3600));
    }

    public function coversSlot(string $slot): bool
    {
        return $this->startHm() <= $slot && $slot < $this->endHm();
    }

    public function canBeCancelledByUser(): bool
    {
        if (! in_array($this->status, self::BLOCKING_STATUSES, true)) {
            return false;
        }

        $startsAt = CarbonImmutable::parse($this->reservation_date->toDateString().' '.$this->startHm());

        return now()->lt($startsAt);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_CONFIRMED => 'Diterima',
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => '-',
        };
    }

    public static function conflictCount(): int
    {
        $count = 0;

        static::query()
            ->whereIn('status', self::BLOCKING_STATUSES)
            ->whereDate('reservation_date', '>=', now()->toDateString())
            ->get()
            ->groupBy(fn (self $reservation): string => $reservation->court_id.'|'.$reservation->reservation_date->toDateString())
            ->each(function ($group) use (&$count): void {
                $items = $group->values();

                for ($i = 1; $i < $items->count(); $i++) {
                    for ($j = 0; $j < $i; $j++) {
                        if ($items[$i]->startHm() < $items[$j]->endHm()
                            && $items[$i]->endHm() > $items[$j]->startHm()) {
                            $count++;
                        }
                    }
                }
            });

        return $count;
    }
}
