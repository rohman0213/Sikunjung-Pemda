<?php

namespace Database\Factories;

use App\Models\Karyawan;
use App\Models\SettingKunjungan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SettingKunjungan>
 */
class SettingKunjunganFactory extends Factory
{
    protected $model = SettingKunjungan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'karyawan_id' => Karyawan::factory(),
            'jabatan' => fake()->randomElement(KaryawanFactory::JABATAN),
            'tanggal' => fake()->unique()->dateTimeBetween('+1 days', '+60 days')->format('Y-m-d'),
            'jam_aktif' => '08:00',
            'jam_non_aktif' => '14:00',
            'status' => SettingKunjungan::STATUS_AKTIF,
        ];
    }

    /**
     * Setting layanan nonaktif.
     */
    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SettingKunjungan::STATUS_NONAKTIF,
        ]);
    }
}
