<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$from, $to] = $this->dateRange($request);
        $base = Reservation::query()->whereBetween('reservation_date', [$from, $to]);

        $stats = [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', Reservation::STATUS_PENDING)->count(),
            'confirmed' => (clone $base)->where('status', Reservation::STATUS_CONFIRMED)->count(),
            'rejected' => (clone $base)->where('status', Reservation::STATUS_REJECTED)->count(),
            'cancelled' => (clone $base)->where('status', Reservation::STATUS_CANCELLED)->count(),
        ];

        $courtStats = (clone $base)
            ->select('court_id')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as confirmed', [Reservation::STATUS_CONFIRMED])
            ->with('court')
            ->groupBy('court_id')
            ->orderByDesc('confirmed')
            ->get();

        $popularHours = (clone $base)
            ->where('status', Reservation::STATUS_CONFIRMED)
            ->select('start_time')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('start_time')
            ->orderByDesc('total')
            ->get();

        $maxPopularHour = max(1, (int) ($popularHours->max('total') ?? 0));

        return view('admin.reports.index', compact(
            'from', 'to', 'stats', 'courtStats', 'popularHours', 'maxPopularHour'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        [$from, $to] = $this->dateRange($request);
        $fileName = "laporan-reservasi-{$from}-{$to}.csv";

        return response()->streamDownload(function () use ($from, $to): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF"); // BOM agar karakter Indonesia terbaca baik di Excel.
            fputcsv($output, ['ID', 'Nama Peminjam', 'Email', 'Lapangan', 'Tanggal', 'Mulai', 'Selesai', 'Status', 'Diajukan', 'Ditinjau']);

            Reservation::query()
                ->with(['user', 'court'])
                ->whereBetween('reservation_date', [$from, $to])
                ->orderBy('reservation_date')
                ->orderBy('start_time')
                ->orderBy('id')
                ->chunk(500, function ($reservations) use ($output): void {
                    foreach ($reservations as $reservation) {
                        fputcsv($output, [
                            $reservation->id,
                            $this->csvSafe($reservation->borrower_name),
                            $this->csvSafe($reservation->user?->email),
                            $this->csvSafe($reservation->court?->name),
                            $reservation->reservation_date->toDateString(),
                            $reservation->startHm(),
                            $reservation->endHm(),
                            $reservation->statusLabel(),
                            $reservation->created_at?->toDateTimeString(),
                            $reservation->reviewed_at?->toDateTimeString(),
                        ]);
                    }
                });

            fclose($output);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** @return array{0: string, 1: string} */
    private function dateRange(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $from = isset($validated['from'])
            ? CarbonImmutable::parse($validated['from'])->toDateString()
            : now()->startOfMonth()->toDateString();
        $to = isset($validated['to'])
            ? CarbonImmutable::parse($validated['to'])->toDateString()
            : now()->toDateString();

        if ($from > $to) {
            throw ValidationException::withMessages([
                'to' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
            ]);
        }

        return [$from, $to];
    }

    /**
     * Mencegah CSV injection: sel yang diawali = + - @ dibaca Excel sebagai rumus.
     */
    private function csvSafe(?string $value): ?string
    {
        if ($value !== null && preg_match('/^[=+\-@\t\r]/', $value) === 1) {
            return "'".$value;
        }

        return $value;
    }
}
