<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewTulisanRequest extends FormRequest
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
        // Aksi tolak dan minta revisi wajib menyertakan catatan.
        $aksi = $this->route('aksi') ?? $this->input('aksi');
        $catatanWajib = in_array($aksi, ['tolak', 'revisi'], strict: true);

        return [
            'catatan' => [$catatanWajib ? 'required' : 'nullable', 'string', 'max:1000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'catatan.required' => 'Catatan editor wajib diisi saat menolak atau meminta revisi.',
            'catatan.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
