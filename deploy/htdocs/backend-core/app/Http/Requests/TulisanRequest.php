<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TulisanRequest extends FormRequest
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
            'judul' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'string', 'in:puisi,cerpen,artikel,esai'],
            'kategori_id' => ['nullable', 'integer', 'exists:kategori,id'],
            'isi' => ['required', 'string', 'min:10'],
            'ringkasan' => ['nullable', 'string', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul tulisan wajib diisi.',
            'judul.max' => 'Judul maksimal 255 karakter.',
            'jenis.required' => 'Jenis tulisan wajib dipilih.',
            'jenis.in' => 'Jenis tulisan harus salah satu dari: puisi, cerpen, artikel, esai.',
            'isi.required' => 'Isi tulisan wajib diisi.',
            'isi.min' => 'Isi tulisan minimal 10 karakter.',
            'ringkasan.max' => 'Ringkasan maksimal 500 karakter.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid.',
        ];
    }
}
