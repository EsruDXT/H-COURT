<?php

namespace App\Http\Requests\Admin;

use App\Http\Controllers\ReservationController;
use App\Models\Court;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'court_id' => ['required', Rule::exists(Court::class, 'id')],
            'reservation_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration' => ['required', 'integer', 'between:1,'.Reservation::MAX_DURATION],
            'purpose' => ['required', Rule::in(ReservationController::PURPOSES)],
            'status' => ['required', Rule::in([
                Reservation::STATUS_PENDING, Reservation::STATUS_CONFIRMED,
                Reservation::STATUS_REJECTED, Reservation::STATUS_CANCELLED,
            ])],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty() || ! in_array($this->input('status'), Reservation::BLOCKING_STATUSES, true)) {
                return;
            }

            $court = Court::find($this->input('court_id'));
            if (! $court) {
                return;
            }

            $date = $this->input('reservation_date');
            $window = $court->operatingWindowForDate($date);
            if ($window === null) {
                $validator->errors()->add('reservation_date', 'Lapangan tidak beroperasi pada tanggal tersebut.');
                return;
            }

            $start = $this->input('start_time');
            if (! in_array($start, $court->timeSlotsForDate($date), true)) {
                $validator->errors()->add('start_time', 'Jam mulai berada di luar jadwal operasional lapangan.');
                return;
            }

            $end = CarbonImmutable::createFromFormat('H:i', $start)->addHours((int) $this->input('duration'))->format('H:i');
            if ($end > $window['closes_at']) {
                $validator->errors()->add('duration', 'Durasi melewati jam tutup lapangan ('.$window['closes_at'].').');
            }
        }];
    }

    public function attributes(): array
    {
        return [
            'court_id' => 'lapangan', 'reservation_date' => 'tanggal', 'start_time' => 'jam mulai',
            'duration' => 'durasi', 'purpose' => 'keperluan', 'status' => 'status',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.', 'in' => ':attribute yang dipilih tidak valid.',
            'exists' => ':attribute yang dipilih tidak valid.', 'date' => ':attribute tidak valid.',
            'date_format' => ':attribute harus berformat JJ:MM.', 'integer' => ':attribute harus berupa angka.',
            'between' => ':attribute harus antara :min dan :max jam.',
        ];
    }
}
