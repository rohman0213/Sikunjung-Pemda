<?php

namespace Database\Factories;

use App\Models\Pemohon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pemohon>
 */
class PemohonFactory extends Factory
{
    protected $model = Pemohon::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->pemohon(),
            'nik' => fake()->unique()->numerify('################'),
            'nama' => fake()->name(),
            'no_telepon' => fake()->numerify('08##########'),
            'alamat' => fake()->address(),
        ];
    }
}
