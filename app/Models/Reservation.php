<?php

namespace App\Models;

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

    /** Status yang membuat jadwal tidak bisa dipesan orang lain. */
    public const BLOCKING_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
    ];

    /** Jam mulai yang bisa dipilih (slot per 1 jam). */
    public const TIME_SLOTS = ['13:00', '14:00', '15:00', '16:00', '17:00'];

    /** Batas jam selesai reservasi. */
    public const DAY_END = '18:00';

    /** Durasi maksimal (jam) per reservasi. */
    public const MAX_DURATION = 3;

    protected $fillable = [
        'user_id',
        'court_id',
        'reservation_date',
        'start_time',
        'end_time',
        'borrower_name',
        'class_institution',
        'phone_number',
        'participant_count',
        'purpose',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
            'participant_count' => 'integer',
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

    /** Apakah reservasi ini menutupi slot jam tertentu (format H:i). */
    public function coversSlot(string $slot): bool
    {
        return $this->startHm() <= $slot && $slot < $this->endHm();
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

    /**
     * Jumlah pasangan reservasi aktif yang saling tumpang tindih (mulai hari ini).
     */
    public static function conflictCount(): int
    {
        $count = 0;

        static::query()
            ->whereIn('status', self::BLOCKING_STATUSES)
            ->whereDate('reservation_date', '>=', now()->toDateString())
            ->get()
            ->groupBy(fn (self $r): string => $r->court_id.'|'.$r->reservation_date->toDateString())
            ->each(function ($group) use (&$count): void {
                $list = $group->values();

                for ($i = 1; $i < $list->count(); $i++) {
                    for ($j = 0; $j < $i; $j++) {
                        if ($list[$i]->startHm() < $list[$j]->endHm() && $list[$i]->endHm() > $list[$j]->startHm()) {
                            $count++;
                        }
                    }
                }
            });

        return $count;
    }
}
