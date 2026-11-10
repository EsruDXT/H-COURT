<?php

namespace App\Http\Requests\Admin;

use App\Models\Court;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourtRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', Rule::in(array_keys(Court::CATEGORIES))],
            'location' => ['required', 'string', 'max:150'],
            'opens_at' => ['required', 'date_format:H:i'],
            'closes_at' => ['required', 'date_format:H:i', 'after:opens_at'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama lapangan',
            'category' => 'kategori',
            'location' => 'lokasi / permukaan',
            'opens_at' => 'jam buka',
            'closes_at' => 'jam tutup',
            'image' => 'gambar',
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
            'in' => ':attribute yang dipilih tidak valid.',
            'date_format' => ':attribute harus berformat JJ:MM.',
            'after' => ':attribute harus setelah jam buka.',
            'image' => ':attribute harus berupa gambar.',
            'mimes' => ':attribute harus berformat jpg, png, atau webp.',
            'image.max' => ':attribute maksimal 2 MB.',
        ];
    }
}
