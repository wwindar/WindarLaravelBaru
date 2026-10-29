<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AdminStoreAgendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'string', 'in:lomba,bedah_buku,open_mic,workshop'],
            'deskripsi' => ['required', 'string'],
            'lokasi' => ['required', 'string', 'max:255'],
            'mulai_pada' => ['required', 'date'],
            'selesai_pada' => ['required', 'date', 'after_or_equal:mulai_pada'],
            'kuota' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul agenda wajib diisi.',
            'jenis.required' => 'Jenis agenda wajib dipilih.',
            'jenis.in' => 'Jenis agenda harus berupa lomba, bedah_buku, open_mic, atau workshop.',
            'deskripsi.required' => 'Deskripsi agenda wajib diisi.',
            'lokasi.required' => 'Lokasi kegiatan wajib diisi.',
            'mulai_pada.required' => 'Waktu mulai agenda wajib ditentukan.',
            'selesai_pada.required' => 'Waktu selesai agenda wajib ditentukan.',
            'selesai_pada.after_or_equal' => 'Waktu selesai tidak boleh lebih awal dari waktu mulai.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException(
            $validator,
            response()->json([
                'message' => 'Validasi data agenda gagal. Silakan periksa kembali input Anda.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}

