<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AdminUpdateArtikelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => ['sometimes', 'required', 'string', 'max:255'],
            'ringkasan' => ['sometimes', 'required', 'string'],
            'isi' => ['sometimes', 'required', 'string'],
            'jenis' => ['sometimes', 'required', 'string', 'in:berita,blog'],
            'kategori_id' => ['sometimes', 'required', 'integer', 'exists:kategori,id'],
            'penulis' => ['sometimes', 'required', 'string', 'max:255'],
            'status' => ['sometimes', 'required', 'string', 'in:draft,terbit'],
            'diterbitkan_pada' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul artikel tidak boleh kosong.',
            'ringkasan.required' => 'Ringkasan artikel tidak boleh kosong.',
            'isi.required' => 'Isi artikel tidak boleh kosong.',
            'jenis.in' => 'Jenis artikel harus berupa berita atau blog.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid.',
            'status.in' => 'Status artikel harus berupa draft atau terbit.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException(
            $validator,
            response()->json([
                'message' => 'Validasi perubahan artikel gagal. Silakan periksa kembali input Anda.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
