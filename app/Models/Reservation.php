<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    // Status reservasi
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    // Status yang membuat jadwal tidak bisa dipesan
    public const BLOCKING_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
    ];

    // Kolom yang boleh diisi melalui mass assignment
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

    // Konversi tanggal menjadi objek Carbon
    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
            'participant_count' => 'integer',
        ];
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Court
    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    // Format jam reservasi
    public function timeRange(): string
    {
        $start = date('H:i', strtotime($this->start_time));
        $end = date('H:i', strtotime($this->end_time));

        return $start . ' - ' . $end;
    }
}
