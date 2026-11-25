<?php

namespace App\Http\Controllers;

use App\Models\Court;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function show(Court $court): RedirectResponse
    {
        return redirect()->route('schedule.index', ['category' => $court->category]);
    }

    public function index(Request $request): View
    {
        $courts = Court::query()->where('is_active', true)->orderBy('name')->get();
        $courtTypes = [
            'futsal' => 'Futsal',
            'basket' => 'Basket',
            'badminton' => 'Badminton',
        ];
        $selectedCategory = $request->query('category', 'futsal');
        if (! array_key_exists($selectedCategory, $courtTypes)) {
            $selectedCategory = 'futsal';
        }

        $weekOffset = min(12, max(0, (int) $request->query('week', 0)));
        $selectedCourts = $courts->where('category', $selectedCategory)->values();

        return view('schedule.index', $this->scheduleData($selectedCourts, $courtTypes, $selectedCategory, $weekOffset));
    }

    /** @param Collection<int, Court> $courts */
    protected function scheduleData(Collection $courts, array $courtTypes, string $selectedCategory, int $weekOffset): array
    {
        $today = CarbonImmutable::today();
        $start = $today->startOfWeek()->addWeeks($weekOffset);
        $days = ['SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB', 'MIN'];
        $dates = [];
        $courtSchedules = [];
        $timeSlotSet = [];
        $priority = ['closed' => 0, 'available' => 1, 'pending' => 2, 'past' => 2, 'booked' => 3];
        $schedule = [];

        foreach ($days as $index => $label) {
            $date = $start->addDays($index);
            $dateValue = $date->toDateString();
            $dates[$label] = $date->format('d/m');

            foreach ($courts as $court) {
                $availability = $court->slotAvailability($dateValue);
                foreach ($availability as $slot => $status) {
                    $timeSlotSet[$slot] = true;
                    $courtSchedules[$court->id][$slot][$label] = $status;
                    $current = $schedule[$slot][$label] ?? 'closed';
                    if (($priority[$status] ?? 0) > ($priority[$current] ?? 0)) {
                        $schedule[$slot][$label] = $status;
                    } else {
                        $schedule[$slot][$label] = $current;
                    }
                }
            }
        }

        $timeSlots = array_keys($timeSlotSet);
        sort($timeSlots);

        return [
            'courts' => $courts,
            'courtTypes' => $courtTypes,
            'selectedCategory' => $selectedCategory,
            'days' => $days,
            'dates' => $dates,
            'timeSlots' => $timeSlots,
            'schedule' => $schedule,
            'courtSchedules' => $courtSchedules,
            'weekOffset' => $weekOffset,
            'weekLabel' => $start->format('d/m/Y').' - '.$start->addDays(6)->format('d/m/Y'),
        ];
    }
}
