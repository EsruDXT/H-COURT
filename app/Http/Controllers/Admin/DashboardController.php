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

        if (! in_array($status, [Reservation::STATUS_PENDING, Reservation::STATUS_CONFIRMED, Reservation::STATUS_REJECTED], true)) {
            $status = null;
        }

        $reservations = Reservation::query()
            ->with(['court', 'user'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'pending' => Reservation::where('status', Reservation::STATUS_PENDING)->count(),
            'confirmed_today' => Reservation::where('status', Reservation::STATUS_CONFIRMED)
                ->whereDate('reservation_date', now()->toDateString())
                ->count(),
            'this_month' => Reservation::whereBetween('reservation_date', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ])->count(),
            'active_courts' => Court::where('is_active', true)->count(),
        ];

        return view('admin.dashboard', compact('reservations', 'stats', 'status'));
    }
}
