<?php

namespace Database\Factories;

use App\Enums\JenisTulisan;
use App\Enums\StatusTulisan;
use App\Models\Tulisan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tulisan>
 */
class TulisanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $judul = fake()->sentence(4);

        return [
            'jenis' => fake()->randomElement(JenisTulisan::cases())->value,
            'slug' => Str::slug($judul).'-'.Str::random(4),
            'judul' => $judul,
            'isi' => fake()->paragraphs(3, true),
            'ringkasan' => fake()->sentence(10),
            'status' => StatusTulisan::Draf,
        ];
    }

    /** Status: draf. */
    public function draf(): static
    {
        return $this->state(fn () => ['status' => StatusTulisan::Draf]);
    }

    /** Status: menunggu persetujuan. */
    public function menunggu(): static
    {
        return $this->state(fn () => ['status' => StatusTulisan::Menunggu]);
    }

    /** Status: terbit. */
    public function terbit(): static
    {
        return $this->state(fn () => [
            'status' => StatusTulisan::Terbit,
            'diterbitkan_pada' => now(),
        ]);
    }

    /** Status: perlu revisi. */
    public function perluRevisi(): static
    {
        return $this->state(fn () => [
            'status' => StatusTulisan::PerluRevisi,
            'catatan_editor' => fake()->sentence(8),
        ]);
    }

    /** Status: ditolak. */
    public function ditolak(): static
    {
        return $this->state(fn () => [
            'status' => StatusTulisan::Ditolak,
            'catatan_editor' => fake()->sentence(8),
        ]);
    }
}
