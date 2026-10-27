<?php

namespace Database\Factories;

use App\Models\Karyawan;
use App\Models\Kegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kegiatan>
 */
class KegiatanFactory extends Factory
{
    protected $model = Kegiatan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $awal = fake()->dateTimeBetween('-10 days', '+10 days');

        return [
            'karyawan_id' => Karyawan::factory(),
            'jabatan' => fake()->randomElement(KaryawanFactory::JABATAN),
            'tanggal_awal' => $awal->format('Y-m-d'),
            'tanggal_akhir' => fake()->dateTimeBetween($awal, (clone $awal)->modify('+7 days'))->format('Y-m-d'),
            'deskripsi_kegiatan' => fake()->sentence(6),
        ];
    }
}
