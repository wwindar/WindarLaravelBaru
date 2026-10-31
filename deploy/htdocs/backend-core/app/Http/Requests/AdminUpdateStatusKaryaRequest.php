<?php

namespace App\Http\Requests;

use App\Enums\StatusKarya;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminUpdateStatusKaryaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::enum(StatusKarya::class)],
            'catatan' => ['nullable', 'string', 'required_if:status,perlu_revisi,ditolak'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status karya wajib ditentukan.',
            'status.enum' => 'Status karya yang dipilih tidak valid.',
            'catatan.required_if' => 'Catatan redaksi wajib diisi untuk status Perlu Revisi atau Ditolak.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException(
            $validator,
            response()->json([
                'message' => 'Validasi perubahan status karya gagal.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
