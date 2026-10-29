<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AdminStoreArtikelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'ringkasan' => ['required', 'string'],
            'isi' => ['required', 'string'],
            'jenis' => ['required', 'string', 'in:berita,blog'],
            'kategori_id' => ['required', 'integer', 'exists:kategori,id'],
            'penulis' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:draft,terbit'],
            'diterbitkan_pada' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul artikel wajib diisi.',
            'ringkasan.required' => 'Ringkasan artikel wajib diisi.',
            'isi.required' => 'Isi artikel wajib diisi.',
            'jenis.required' => 'Jenis artikel wajib dipilih (berita atau blog).',
            'jenis.in' => 'Jenis artikel harus berupa berita atau blog.',
            'kategori_id.required' => 'Kategori artikel wajib dipilih.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid.',
            'penulis.required' => 'Nama penulis artikel wajib diisi.',
            'status.required' => 'Status artikel wajib ditentukan (draft atau terbit).',
            'status.in' => 'Status artikel harus berupa draft atau terbit.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException(
            $validator,
            response()->json([
                'message' => 'Validasi data artikel gagal. Silakan periksa kembali input Anda.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}

