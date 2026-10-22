<?php

namespace App\Http\Requests;

use App\Models\Layanan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StorePengajuanRequest extends FormRequest
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
            'layanan_id' => ['required', 'integer', 'exists:layanan,id'],
            'nama_pemohon' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'program_studi' => ['required', 'string', 'max:255'],
            'keperluan' => ['required', 'string'],
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
            'layanan_id.required' => 'Layanan wajib dipilih.',
            'layanan_id.integer' => 'ID layanan harus berupa angka.',
            'layanan_id.exists' => 'Layanan yang dipilih tidak valid atau tidak terdaftar.',
            'nama_pemohon.required' => 'Nama pemohon wajib diisi.',
            'nama_pemohon.string' => 'Nama pemohon harus berupa teks.',
            'nama_pemohon.max' => 'Nama pemohon maksimal 255 karakter.',
            'nim.required' => 'NIM wajib diisi.',
            'nim.string' => 'NIM harus berupa teks.',
            'nim.max' => 'NIM maksimal 50 karakter.',
            'email.required' => 'Email pemohon wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'program_studi.required' => 'Program studi wajib diisi.',
            'program_studi.string' => 'Program studi harus berupa teks.',
            'program_studi.max' => 'Program studi maksimal 255 karakter.',
            'keperluan.required' => 'Keperluan pengajuan wajib diisi.',
            'keperluan.string' => 'Keperluan harus berupa teks.',
            'dokumen.required' => 'Dokumen persyaratan wajib dilampirkan.',
            'dokumen.array' => 'Dokumen persyaratan harus berupa daftar array.',
            'dokumen.min' => 'Minimal satu dokumen harus dilampirkan.',
            'dokumen.*.required' => 'Nama dokumen lampiran tidak boleh kosong.',
            'dokumen.*.string' => 'Nama dokumen lampiran harus berupa teks.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $layananId = $this->input('layanan_id');
            if (! $layananId) {
                return;
            }

            $layanan = Layanan::find($layananId);
            if (! $layanan) {
                return;
            }

            if (! $layanan->aktif) {
                $validator->errors()->add('layanan_id', 'Layanan yang dipilih saat ini sedang tidak aktif.');
            }

            $persyaratanWajib = $layanan->persyaratan ?? [];
            $dokumenSubmitted = array_map(fn ($doc) => trim((string) $doc), (array) $this->input('dokumen', []));

            $missing = [];
            foreach ($persyaratanWajib as $syarat) {
                $found = false;
                foreach ($dokumenSubmitted as $doc) {
                    if (strcasecmp(trim($doc), trim($syarat)) === 0) {
                        $found = true;
                        break;
                    }
                }

                if (! $found) {
                    $missing[] = $syarat;
                }
            }

            if (! empty($missing)) {
                $validator->errors()->add(
                    'dokumen',
                    'Dokumen persyaratan belum lengkap. Dokumen yang belum dilampirkan: '.implode(', ', $missing).'.'
                );
            }
        });
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException(
            $validator,
            response()->json([
                'message' => 'Validasi gagal. Silakan periksa kembali data yang Anda kirimkan.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
