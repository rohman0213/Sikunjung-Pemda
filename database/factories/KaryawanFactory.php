<?php

namespace Database\Factories;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Karyawan>
 */
class KaryawanFactory extends Factory
{
    protected $model = Karyawan::class;

    /**
     * Jabatan yang dipakai pada data contoh.
     *
     * @var array<int, string>
     */
    public const JABATAN = [
        'Kepala Dinas',
        'Sekretaris Dinas',
        'Kepala Bidang Pelayanan',
        'Kepala Bidang Umum',
        'Staf Pelayanan',
        'Staf Umum',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->karyawan(),
            'nik' => fake()->unique()->numerify('################'),
            'nama' => fake()->name(),
            'jabatan' => fake()->randomElement(self::JABATAN),
            'no_telepon' => fake()->numerify('08##########'),
        ];
    }
}
