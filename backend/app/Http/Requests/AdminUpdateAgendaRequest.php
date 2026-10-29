<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AdminUpdateAgendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => ['sometimes', 'required', 'string', 'max:255'],
            'jenis' => ['sometimes', 'required', 'string', 'in:lomba,bedah_buku,open_mic,workshop'],
            'deskripsi' => ['sometimes', 'required', 'string'],
            'lokasi' => ['sometimes', 'required', 'string', 'max:255'],
            'mulai_pada' => ['sometimes', 'required', 'date'],
            'selesai_pada' => ['sometimes', 'required', 'date'],
            'kuota' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul agenda tidak boleh kosong.',
            'jenis.in' => 'Jenis agenda harus berupa lomba, bedah_buku, open_mic, atau workshop.',
            'lokasi.required' => 'Lokasi kegiatan tidak boleh kosong.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException(
            $validator,
            response()->json([
                'message' => 'Validasi perubahan agenda gagal. Silakan periksa kembali input Anda.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}

