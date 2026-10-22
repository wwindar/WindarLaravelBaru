<?php

namespace App\Http\Requests;

use App\Enums\StatusPengajuan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateStatusPengajuanRequest extends FormRequest
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
        $statusValues = array_column(StatusPengajuan::cases(), 'value');

        return [
            'status' => ['required', 'string', Rule::in($statusValues)],
            'catatan' => [
                'nullable',
                'string',
                Rule::requiredIf(fn () => in_array($this->input('status'), [
                    StatusPengajuan::PERLU_PERBAIKAN->value,
                    StatusPengajuan::DITOLAK->value,
                ], true)),
            ],
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
            'status.required' => 'Status pengajuan wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid. Status valid: diajukan, diperiksa, perlu_perbaikan, disetujui, selesai, ditolak.',
            'catatan.required' => 'Catatan wajib diisi apabila status adalah Perlu Perbaikan atau Ditolak.',
            'catatan.string' => 'Catatan harus berupa teks.',
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
                'message' => 'Validasi perubahan status gagal. Periksa kembali data yang dikirimkan.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
