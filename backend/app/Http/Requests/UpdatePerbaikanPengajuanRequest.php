<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdatePerbaikanPengajuanRequest extends FormRequest
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
            'nim' => ['required', 'string', 'max:50'],
            'keperluan' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'program_studi' => ['required', 'string', 'max:255'],
            'dokumen' => ['required', 'array', 'min:1'],
            'dokumen.*' => ['required', 'string'],
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
            'nim.required' => 'NIM wajib diisi untuk verifikasi pengajuan.',
            'nim.string' => 'NIM harus berupa teks.',
            'nim.max' => 'NIM maksimal 50 karakter.',
            'keperluan.required' => 'Keperluan pengajuan wajib diisi.',
            'keperluan.string' => 'Keperluan harus berupa teks.',
            'email.required' => 'Email pemohon wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'program_studi.required' => 'Program studi wajib diisi.',
            'program_studi.string' => 'Program studi harus berupa teks.',
            'program_studi.max' => 'Program studi maksimal 255 karakter.',
            'dokumen.required' => 'Dokumen perbaikan wajib disertakan.',
            'dokumen.array' => 'Dokumen perbaikan harus berupa daftar array.',
            'dokumen.min' => 'Minimal satu dokumen harus dilampirkan.',
            'dokumen.*.required' => 'Nama dokumen lampiran tidak boleh kosong.',
            'dokumen.*.string' => 'Nama dokumen lampiran harus berupa teks.',
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
                'message' => 'Validasi perbaikan pengajuan gagal. Silakan periksa kembali data yang Anda kirimkan.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
