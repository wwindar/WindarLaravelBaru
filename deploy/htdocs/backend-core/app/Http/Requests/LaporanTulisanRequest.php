<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaporanTulisanRequest extends FormRequest
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
            'nama_pelapor' => ['required', 'string', 'max:100'],
            'email_pelapor' => ['required', 'email', 'max:150'],
            'alasan' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'nama_pelapor.required' => 'Nama Anda wajib diisi.',
            'email_pelapor.required' => 'Email Anda wajib diisi.',
            'email_pelapor.email' => 'Format email tidak valid.',
            'alasan.required' => 'Alasan pelaporan wajib diisi.',
            'alasan.min' => 'Alasan pelaporan minimal 10 karakter.',
        ];
    }
}
