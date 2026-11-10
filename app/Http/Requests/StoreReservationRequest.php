<?php

namespace App\Http\Requests;

use App\Http\Controllers\ReservationController;
use App\Models\Court;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'court' => ['required', Rule::exists(Court::class, 'slug')->where('is_active', true)],
            'date' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:'.now()->addDays(5)->toDateString()],
            'time' => ['required', Rule::in(Reservation::TIME_SLOTS)],
            'duration' => ['required', 'integer', 'between:1,'.Reservation::MAX_DURATION],
            'full_name' => ['required', 'string', 'max:100'],
            'class_institution' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:20'],
            'participant_count' => ['required', 'integer', 'min:1'],
            'purpose' => ['required', Rule::in(ReservationController::PURPOSES)],
            'notes' => ['nullable', 'string', 'max:500'],
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

                $end = CarbonImmutable::createFromFormat('H:i', $this->input('time'))
                    ->addHours((int) $this->input('duration'))
                    ->format('H:i');

                if ($end > Reservation::DAY_END) {
                    $validator->errors()->add('duration', 'Durasi melewati jam operasional (maksimal sampai '.Reservation::DAY_END.').');
                } elseif ($this->input('date') === now()->toDateString() && $this->input('time') <= now()->format('H:i')) {
                    $validator->errors()->add('time', 'Jam yang dipilih sudah lewat.');
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
            'court' => 'lapangan',
            'date' => 'tanggal',
            'time' => 'jam',
            'duration' => 'durasi',
            'full_name' => 'nama lengkap',
            'class_institution' => 'kelas / instansi',
            'phone_number' => 'nomor HP',
            'participant_count' => 'jumlah peserta',
            'purpose' => 'keperluan',
            'notes' => 'catatan',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'min' => ':attribute minimal :min.',
            'between' => ':attribute harus antara :min dan :max jam.',
            'integer' => ':attribute harus berupa angka.',
            'date' => ':attribute tidak valid.',
            'exists' => ':attribute yang dipilih tidak valid.',
            'in' => ':attribute yang dipilih tidak valid.',
            'after_or_equal' => ':attribute tidak boleh sebelum hari ini.',
            'before_or_equal' => ':attribute maksimal 6 hari ke depan.',
        ];
    }
}
