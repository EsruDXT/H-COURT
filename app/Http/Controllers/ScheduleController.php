<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $courts = $this->activeCourts();

        $courtTypes = [
            'futsal' => 'Futsal',
            'basket' => 'Basket',
            'badminton' => 'Badminton',
        ];

        $selectedCategory = $request->query('category', 'futsal');

        if (! array_key_exists($selectedCategory, $courtTypes)) {
            $selectedCategory = 'futsal';
        }

        // Mengambil minggu yang dipilih dari URL.
        // Nilai negatif dikembalikan menjadi 0.
        $weekOffset = max(0, (int) $request->query('week', 0));

        $selectedCourts = $courts
            ->where('category', $selectedCategory)
            ->values();

        return view('schedule.index', $this->scheduleData(
            $selectedCourts,
            $courtTypes,
            $selectedCategory,
            $weekOffset
        ));
    }

    /**
     * @return Collection<int, Court>
     */
    protected function activeCourts(): Collection
    {
        return Court::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * @param Collection<int, Court> $courts
     * @param array<string, string> $courtTypes
     * @return array<string, mixed>
     */
    protected function scheduleData(
        Collection $courts,
        array $courtTypes,
        string $selectedCategory,
        int $weekOffset
    ): array {
        $today = CarbonImmutable::today();

        // Menentukan hari Senin sebagai awal minggu.
        // Jika hari ini Minggu, mulai dari Senin berikutnya.
        $start = $today->isSunday()
            ? $today->addDay()
            : $today->startOfWeek();

        // Bergeser sesuai minggu yang dipilih.
        $start = $start->addWeeks($weekOffset);

        // Tetap mempertahankan 6 hari seperti implementasi awal.
        $days = ['SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB'];

        $dates = [];
        $schedule = [];
        $courtSchedules = [];

        foreach ($days as $index => $label) {
            $date = $start->addDays($index);

            $dates[$label] = $date->format('d/m');

            foreach ($courts as $court) {
                $availability = $court->slotAvailability(
                    $date->toDateString()
                );

                foreach (Reservation::TIME_SLOTS as $slot) {
                    $courtSchedules[$court->id][$slot][$label] =
                        $availability[$slot] ?? 'available';

                    $currentStatus = $schedule[$slot][$label] ?? 'available';
                    $newStatus = $availability[$slot] ?? 'available';

                    // Prioritaskan booked, kemudian pending.
                    $priority = [
                        'available' => 0,
                        'pending' => 1,
                        'booked' => 2,
                    ];

                    if (
                        ($priority[$newStatus] ?? 0) >
                        ($priority[$currentStatus] ?? 0)
                    ) {
                        $schedule[$slot][$label] = $newStatus;
                    } else {
                        $schedule[$slot][$label] = $currentStatus;
                    }
                }
            }
        }

        // Label periode minggu yang sedang ditampilkan.
        $weekLabel = $start->format('d/m/Y')
            . ' - '
            . $start->addDays(5)->format('d/m/Y');

        return [
            'courts' => $courts,
            'courtTypes' => $courtTypes,
            'selectedCategory' => $selectedCategory,
            'days' => $days,
            'dates' => $dates,
            'timeSlots' => Reservation::TIME_SLOTS,
            'schedule' => $schedule,
            'courtSchedules' => $courtSchedules,

            // Data navigasi minggu.
            'weekOffset' => $weekOffset,
            'weekLabel' => $weekLabel,
        ];
    }
}
