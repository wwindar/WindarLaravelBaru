<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class KirimKaryaRequest extends FormRequest
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
            'judul' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'string', 'in:puisi,cerpen,esai'],
            'isi' => ['required', 'string', 'min:50'],
            'nama_penulis' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    $domain = strtolower(substr(strrchr($value, '@'), 1));
                    $allowedDomains = ['umpku.ac.id', 'student.umpku.ac.id', 'itspku.ac.id', 'students.itspku.ac.id', 'student.univ.ac.id'];
                    
                    $isAllowed = false;
                    foreach ($allowedDomains as $allowed) {
                        if ($domain === $allowed || str_ends_with($domain, '.' . $allowed)) {
                            $isAllowed = true;
                            break;
                        }
                    }

                    if (! $isAllowed) {
                        $fail('Hanya mahasiswa civitas akademika yang dapat mengirimkan naskah (gunakan email kampus resmi seperti @students.itspku.ac.id, @itspku.ac.id, @umpku.ac.id, atau @student.umpku.ac.id).');
                    }
                },
            ],
        ];
    }

    /**
     * Custom error messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul karya wajib diisi.',
            'judul.max' => 'Judul karya maksimal 255 karakter.',
            'jenis.required' => 'Jenis karya wajib dipilih.',
            'jenis.in' => 'Jenis karya harus berupa puisi, cerpen, atau esai.',
            'isi.required' => 'Isi naskah karya wajib diisi.',
            'isi.min' => 'Isi naskah karya minimal 50 karakter.',
            'nama_penulis.required' => 'Nama penulis wajib diisi.',
            'nama_penulis.max' => 'Nama penulis maksimal 255 karakter.',
            'email.required' => 'Email penulis wajib diisi.',
            'email.email' => 'Format email penulis tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
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
                'message' => 'Validasi pengiriman karya gagal. Silakan periksa kembali data Anda.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
