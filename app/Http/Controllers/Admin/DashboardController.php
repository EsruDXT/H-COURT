<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        if (!in_array($status, [Reservation::STATUS_PENDING, Reservation::STATUS_CONFIRMED, Reservation::STATUS_REJECTED], true)) {
            $status = null;
        }

        $reservations = Reservation::query()
            ->with(['court', 'user'])
            ->when($status, fn($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $today = today()->toDateString();

        $stats = [
            // Reservasi yang masih menunggu persetujuan.
            'pending' => Reservation::where(
                'status',
                Reservation::STATUS_PENDING
            )->count(),

            // Reservasi yang sudah diterima dan tanggalnya hari ini.
            'confirmed_today' => Reservation::where('status', Reservation::STATUS_CONFIRMED)
                ->whereDate('reviewed_at', now()->toDateString())
                ->count(),

            // Semua reservasi pada bulan berjalan.
            'this_month' => Reservation::whereBetween('reservation_date', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ])->count(),

            // Jumlah lapangan yang aktif.
            'active_courts' => Court::where('is_active', true)->count(),
        ];

        return view('admin.dashboard', compact('reservations', 'stats', 'status'));
    }
}
