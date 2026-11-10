<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $courts = $this->activeCourts();

        $court = $courts->firstWhere('slug', $request->query('court')) ?? $courts->first();

        return view('schedule.index', $this->scheduleData($courts, $court));
    }

    public function show(string $court): View
    {
        $courts = $this->activeCourts();

        $selected = $courts->firstWhere('slug', $court) ?? abort(404);

        return view('schedule.show', $this->scheduleData($courts, $selected));
    }

    /**
     * @return Collection<int, Court>
     */
    protected function activeCourts(): Collection
    {
        return Court::query()->where('is_active', true)->orderBy('id')->get();
    }

    /**
     * @param  Collection<int, Court>  $courts
     * @return array<string, mixed>
     */
    protected function scheduleData(Collection $courts, ?Court $court): array
    {
        $today = CarbonImmutable::today();
        $start = $today->isSunday() ? $today->addDay() : $today->startOfWeek();

        $days = ['SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB'];
        $dates = [];
        $schedule = [];

        foreach ($days as $index => $label) {
            $date = $start->addDays($index);
            $dates[$label] = $date->format('d/m');

            $availability = $court ? $court->slotAvailability($date->toDateString()) : [];

            foreach (Reservation::TIME_SLOTS as $slot) {
                $schedule[$slot][$label] = $availability[$slot] ?? 'available';
            }
        }

        $courtTypes = $courts
            ->mapWithKeys(fn (Court $item): array => [$item->slug => Str::replaceFirst('Lapangan ', '', $item->name)])
            ->all();

        return [
            'court' => $court,
            'courtTypes' => $courtTypes,
            'days' => $days,
            'dates' => $dates,
            'timeSlots' => Reservation::TIME_SLOTS,
            'schedule' => $schedule,
        ];
    }
}
