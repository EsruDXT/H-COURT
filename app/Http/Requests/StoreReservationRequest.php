<?php

namespace App\Http\Requests;

use App\Http\Controllers\ReservationController;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Court;

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
            'time' => ['required', Rule::in(ReservationController::TIME_SLOTS)],
            'full_name' => ['required', 'string', 'max:100'],
            'class_institution' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:20'],
            'participant_count' => ['required', 'integer', 'min:1'],
            'purpose' => ['required', Rule::in(ReservationController::PURPOSES)],
            'notes' => ['nullable', 'string', 'max:500'],
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
            'integer' => ':attribute harus berupa angka.',
            'in' => ':attribute yang dipilih tidak valid.',
            'after_or_equal' => ':attribute tidak boleh sebelum hari ini.',
            'before_or_equal' => ':attribute maksimal 6 hari ke depan.',
        ];
    }
}