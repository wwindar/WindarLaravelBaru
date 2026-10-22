<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateLayananRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['sometimes', 'required', 'string', 'max:255'],
            'deskripsi' => ['sometimes', 'required', 'string'],
            'persyaratan' => ['sometimes', 'required', 'array', 'min:1'],
            'persyaratan.*' => ['required_with:persyaratan', 'string'],
            'estimasi_hari' => ['sometimes', 'required', 'integer', 'min:1'],
            'aktif' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Custom validation messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama layanan wajib diisi.',
            'nama.string' => 'Nama layanan harus berupa teks.',
            'nama.max' => 'Nama layanan maksimal 255 karakter.',
            'deskripsi.required' => 'Deskripsi layanan wajib diisi.',
            'deskripsi.string' => 'Deskripsi layanan harus berupa teks.',
            'persyaratan.required' => 'Persyaratan dokumen wajib diisi.',
            'persyaratan.array' => 'Persyaratan dokumen harus berupa array.',
            'persyaratan.min' => 'Minimal satu persyaratan dokumen harus dicantumkan.',
            'persyaratan.*.required_with' => 'Item persyaratan tidak boleh kosong.',
            'persyaratan.*.string' => 'Item persyaratan harus berupa teks.',
            'estimasi_hari.required' => 'Estimasi hari proses wajib diisi.',
            'estimasi_hari.integer' => 'Estimasi hari harus berupa angka bulat.',
            'estimasi_hari.min' => 'Estimasi hari minimal 1 hari.',
            'aktif.boolean' => 'Status aktif harus bernilai boolean (true atau false).',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException(
            $validator,
            response()->json([
                'message' => 'Validasi perubahan layanan gagal. Silakan periksa kembali data yang Anda kirimkan.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
