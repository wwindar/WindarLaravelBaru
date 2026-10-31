<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class RevisiKaryaRequest extends FormRequest
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
            'email' => ['required', 'string', 'email'],
            'judul' => ['sometimes', 'required', 'string', 'max:255'],
            'isi' => ['sometimes', 'required', 'string', 'min:50'],
        ];
    }

    /**
     * Custom messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email pemohon karya wajib disertakan.',
            'email.email' => 'Format email tidak valid.',
            'judul.required' => 'Judul karya tidak boleh kosong.',
            'judul.max' => 'Judul karya maksimal 255 karakter.',
            'isi.required' => 'Isi naskah karya tidak boleh kosong.',
            'isi.min' => 'Isi naskah karya minimal 50 karakter.',
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
                'message' => 'Validasi perbaikan karya gagal. Silakan periksa kembali data Anda.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
