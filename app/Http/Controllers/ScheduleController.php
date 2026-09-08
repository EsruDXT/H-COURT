<?php

namespace App\Http\Controllers;

class ScheduleController extends Controller
{
    public function index()
    {
        $courtTypes = [
            'futsal'    => 'Futsal',
            'basket'    => 'Basket',
            'badminton' => 'Badminton',
        ];

        $days = ['SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB'];

        $timeSlots = ['13:00', '14:00', '15:00', '16:00', '17:00'];

        $schedule = [
            '13:00' => ['SEN' => 'available', 'SEL' => 'booked',    'RAB' => 'available', 'KAM' => 'available', 'JUM' => 'available', 'SAB' => 'available'],
            '14:00' => ['SEN' => 'booked',    'SEL' => 'booked',    'RAB' => 'available', 'KAM' => 'booked',    'JUM' => 'available', 'SAB' => 'available'],
            '15:00' => ['SEN' => 'available', 'SEL' => 'available', 'RAB' => 'available', 'KAM' => 'available', 'JUM' => 'booked',    'SAB' => 'booked'],
            '16:00' => ['SEN' => 'available', 'SEL' => 'available', 'RAB' => 'available', 'KAM' => 'pending',   'JUM' => 'booked',    'SAB' => 'available'],
            '17:00' => ['SEN' => 'booked',    'SEL' => 'available', 'RAB' => 'booked',    'KAM' => 'available', 'JUM' => 'available', 'SAB' => 'booked'],
        ];

        return view('schedule.index', compact('courtTypes', 'days', 'timeSlots', 'schedule'));
    }

    public function show(string $court)
    {
        return view('schedule.show', compact('court'));
    }
}