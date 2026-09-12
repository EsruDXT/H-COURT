<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    protected function courts(): array
    {
        return [
            [
                'slug'  => 'futsal',
                'name'  => 'Lapangan Futsal',
                'type'  => 'Outdoor - sintesis',
                'image' => 'images/courts/futsal.jpg',
            ],
            [
                'slug'  => 'basket',
                'name'  => 'Lapangan Basket',
                'type'  => 'Indoor - gedung olahraga',
                'image' => 'images/courts/basket.jpg',
            ],
            [
                'slug'  => 'badminton',
                'name'  => 'Lapangan Badminton',
                'type'  => 'Indoor - gedung olahraga',
                'image' => 'images/courts/badminton.jpg',
            ],
        ];
    }

    public function index()
    {
        $reservations = [
            [
                'court'  => 'Lapangan Futsal',
                'date'   => 'Senin, 18 Agustus 2026',
                'time'   => '15:00 - 16:00',
                'status' => 'confirmed',
            ],
            [
                'court'  => 'Lapangan Badminton',
                'date'   => 'Jumat, 22 Agustus 2026',
                'time'   => '16:00 - 17:00',
                'status' => 'pending',
            ],
            [
                'court'  => 'Lapangan Basket',
                'date'   => 'Rabu, 20 Agustus 2026',
                'time'   => '14:00 - 15:00',
                'status' => 'rejected',
            ],
        ];

        return view('reservation.index', compact('reservations'));
    }

    public function create()
    {
        $courts = $this->courts();

        // Dummy 6 upcoming days for the "select schedule" step
        $days = collect(range(0, 5))->map(function ($i) {
            $date = now()->addDays($i);
            return [
                'label' => strtoupper($date->translatedFormat('D')), // MON, TUE, etc.
                'date'  => $date->format('d'),
                'value' => $date->toDateString(),
                'full'  => $date->translatedFormat('l, d F Y'),
            ];
        });

        $timeSlots = ['13:00', '14:00', '15:00', '16:00', '17:00'];

        return view('reservation.create', compact('courts', 'days', 'timeSlots'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'court'             => 'required|string',
            'court_label'       => 'required|string',
            'date'              => 'required|date',
            'time'              => 'required|string',
            'full_name'         => 'required|string|max:100',
            'class_institution' => 'required|string|max:100',
            'phone_number'      => 'required|string|max:20',
            'participant_count' => 'required|integer|min:1',
            'purpose'           => 'required|string|max:100',
            'notes'             => 'nullable|string|max:500',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reservation submitted successfully.',
            'data'    => [
                'court'  => $validated['court_label'],
                'date'   => Carbon::parse($validated['date'])->translatedFormat('l, d F Y'),
                'time'   => $validated['time'] . ' - ' . Carbon::createFromFormat('H:i', $validated['time'])->addHour()->format('H:i'),
                'status' => 'Menunggu Persetujuan',
            ],
        ]);
    }
}