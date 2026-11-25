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
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'court' => ['required', Rule::exists(Court::class, 'slug')->where('is_active', true)],
            'date' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:'.now()->addDays(5)->toDateString()],
            'time' => ['required', 'date_format:H:i'],
            'duration' => ['required', 'integer', 'between:1,'.Reservation::MAX_DURATION],
            'full_name' => ['required', 'string', 'max:100'],
            'class_institution' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:20'],
            'participant_count' => ['required', 'integer', 'min:1'],
            'purpose' => ['required', Rule::in(ReservationController::PURPOSES)],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $court = Court::query()->where('slug', $this->input('court'))->where('is_active', true)->first();
            if (! $court) {
                $validator->errors()->add('court', 'Lapangan tidak ditemukan atau sedang nonaktif.');
                return;
            }

            $date = $this->input('date');
            $time = $this->input('time');
            $window = $court->operatingWindowForDate($date);
            if ($window === null) {
                $validator->errors()->add('date', 'Lapangan tutup pada tanggal yang dipilih.');
                return;
            }

            if (! in_array($time, $court->timeSlotsForDate($date), true)) {
                $validator->errors()->add('time', 'Jam tersebut berada di luar jadwal operasional lapangan.');
                return;
            }

            $end = CarbonImmutable::createFromFormat('H:i', $time)->addHours((int) $this->input('duration'))->format('H:i');
            if ($end > $window['closes_at']) {
                $validator->errors()->add('duration', 'Durasi melewati jam tutup lapangan ('.$window['closes_at'].').');
            } elseif ($date === now()->toDateString() && $time <= now()->format('H:i')) {
                $validator->errors()->add('time', 'Jam yang dipilih sudah lewat.');
            }
        }];
    }

    public function attributes(): array
    {
        return [
            'court' => 'lapangan', 'date' => 'tanggal', 'time' => 'jam', 'duration' => 'durasi',
            'full_name' => 'nama lengkap', 'class_institution' => 'kelas / instansi',
            'phone_number' => 'nomor HP', 'participant_count' => 'jumlah peserta',
            'purpose' => 'keperluan', 'notes' => 'catatan',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.', 'max' => ':attribute maksimal :max karakter.',
            'min' => ':attribute minimal :min.', 'between' => ':attribute harus antara :min dan :max jam.',
            'integer' => ':attribute harus berupa angka.', 'date' => ':attribute tidak valid.',
            'exists' => ':attribute yang dipilih tidak valid.', 'in' => ':attribute yang dipilih tidak valid.',
            'after_or_equal' => ':attribute tidak boleh sebelum hari ini.',
            'before_or_equal' => ':attribute maksimal 6 hari ke depan.',
        ];
    }
}
