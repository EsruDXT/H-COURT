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

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'court_id' => ['required', Rule::exists(Court::class, 'id')],
            'reservation_date' => ['required', 'date'],
            'start_time' => ['required', Rule::in(Reservation::TIME_SLOTS)],
            'duration' => ['required', 'integer', 'between:1,'.Reservation::MAX_DURATION],
            'purpose' => ['required', Rule::in(ReservationController::PURPOSES)],
            'status' => ['required', Rule::in([
                Reservation::STATUS_PENDING,
                Reservation::STATUS_CONFIRMED,
                Reservation::STATUS_REJECTED,
                Reservation::STATUS_CANCELLED,
            ])],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $end = CarbonImmutable::createFromFormat('H:i', $this->input('start_time'))
                    ->addHours((int) $this->input('duration'))
                    ->format('H:i');

                if ($end > Reservation::DAY_END) {
                    $validator->errors()->add('duration', 'Durasi melewati jam operasional (maksimal sampai '.Reservation::DAY_END.').');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'court_id' => 'lapangan',
            'reservation_date' => 'tanggal',
            'start_time' => 'jam mulai',
            'duration' => 'durasi',
            'purpose' => 'keperluan',
            'status' => 'status',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'in' => ':attribute yang dipilih tidak valid.',
            'exists' => ':attribute yang dipilih tidak valid.',
            'date' => ':attribute tidak valid.',
            'integer' => ':attribute harus berupa angka.',
            'between' => ':attribute harus antara :min dan :max jam.',
        ];
    }
}
