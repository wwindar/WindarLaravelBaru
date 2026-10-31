<?php

namespace Tests\Feature;

use App\Models\Agenda;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_publik_dapat_melihat_daftar_agenda_dan_filter_waktu(): void
    {
        // Agenda Lampau
        Agenda::create([
            'slug' => 'agenda-lampau',
            'judul' => 'Agenda Masa Lampau',
            'jenis' => 'workshop',
            'deskripsi' => 'Deskripsi kegiatan lampau.',
            'lokasi' => 'Gedung A',
            'mulai_pada' => Carbon::now()->subDays(10),
            'selesai_pada' => Carbon::now()->subDays(9),
            'kuota' => 20,
        ]);

        // Agenda Mendatang
        Agenda::create([
            'slug' => 'agenda-mendatang',
            'judul' => 'Agenda Masa Mendatang',
            'jenis' => 'lomba',
            'deskripsi' => 'Deskripsi kegiatan mendatang.',
            'lokasi' => 'Gedung B',
            'mulai_pada' => Carbon::now()->addDays(5),
            'selesai_pada' => Carbon::now()->addDays(6),
            'kuota' => 50,
        ]);

        $responseAll = $this->getJson('/api/agenda');
        $responseAll->assertStatus(200)
            ->assertJsonCount(2, 'data');

        // Filter mendatang
        $responseMendatang = $this->getJson('/api/agenda?waktu=mendatang');
        $responseMendatang->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'agenda-mendatang');

        // Filter lampau
        $responseLampau = $this->getJson('/api/agenda?waktu=lampau');
        $responseLampau->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'agenda-lampau');
    }

    public function test_publik_dapat_melihat_detail_agenda(): void
    {
        $agenda = Agenda::create([
            'slug' => 'panggung-puisi-terbuka',
            'judul' => 'Panggung Puisi Terbuka',
            'jenis' => 'open_mic',
            'deskripsi' => 'Panggung ekspresi baca puisi bersama.',
            'lokasi' => 'Amphitheater Kampus',
            'mulai_pada' => Carbon::now()->addDays(3),
            'selesai_pada' => Carbon::now()->addDays(3)->addHours(4),
            'kuota' => 100,
        ]);

        $response = $this->getJson('/api/agenda/panggung-puisi-terbuka');

        $response->assertStatus(200)
            ->assertJsonPath('data.slug', 'panggung-puisi-terbuka')
            ->assertJsonPath('data.jenis', 'open_mic');

        $responseNotFound = $this->getJson('/api/agenda/slug-tidak-ada');
        $responseNotFound->assertStatus(404)
            ->assertJson([
                'message' => 'Agenda kegiatan sastra tidak ditemukan.',
            ]);
    }
}
