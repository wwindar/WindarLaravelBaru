<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $allowedDomains = $this->resolveAllowedDomains();

        return [
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'alpha_dash', 'min:3', 'max:50', 'unique:users,username'],
            'email' => [
                'required',
                'email',
                'max:150',
                'unique:users,email',
                function (string $attribute, mixed $value, \Closure $fail) use ($allowedDomains): void {
                    $domain = strtolower(substr(strrchr($value, '@'), 1));

                    $isAllowed = false;
                    foreach ($allowedDomains as $allowed) {
                        if ($domain === $allowed) {
                            $isAllowed = true;
                            break;
                        }
                    }

                    if (! $isAllowed) {
                        $domainsText = implode(', ', array_map(fn ($d) => "@{$d}", $allowedDomains));
                        $fail("Pendaftaran hanya untuk civitas akademika. Gunakan email resmi kampus: {$domainsText}.");
                    }
                },
            ],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, tanda strip, atau garis bawah.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.unique' => 'Username sudah digunakan, pilih yang lain.',
            'email.required' => 'Email kampus wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar. Silakan langsung masuk.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ];
    }

    /** @return string[] */
    private function resolveAllowedDomains(): array
    {
        $raw = config('app.kampus_email_domains', env('KAMPUS_EMAIL_DOMAINS', ''));

        if (is_array($raw)) {
            return array_filter(array_map('trim', $raw));
        }

        return array_filter(array_map('trim', explode(',', (string) $raw)));
    }
}
