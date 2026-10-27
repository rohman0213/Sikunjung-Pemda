<?php

namespace Database\Factories;

use App\Models\Karyawan;
use App\Models\Kunjungan;
use App\Models\Pemohon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kunjungan>
 */
class KunjunganFactory extends Factory
{
    protected $model = Kunjungan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pemohon_id' => Pemohon::factory(),
            'karyawan_id' => Karyawan::factory(),
            'tanggal' => fake()->unique()->dateTimeBetween('+1 days', '+60 days')->format('Y-m-d'),
            'jam' => fake()->randomElement(['08:00', '09:00', '10:00', '11:00', '13:00']),
            'keperluan' => fake()->sentence(4),
            'status' => 'menunggu',
            'catatan' => null,
        ];
    }

    /**
     * Kunjungan yang sudah disetujui.
     */
    public function disetujui(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'disetujui',
        ]);
    }

    /**
     * Kunjungan yang ditolak beserta catatannya.
     */
    public function ditolak(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ditolak',
            'catatan' => fake()->sentence(6),
        ]);
    }
}
