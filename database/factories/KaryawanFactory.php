<?php

namespace Database\Factories;

use App\Models\Departemens;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Karyawan>
 */
class KaryawanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nip' => fake()->unique()->numerify('NIP##########'),
            'nama_karyawan' => fake()->name(),
            'alamat' => fake()->address(),
            'departemen_id' => Departemens::factory(),
            'no_hp' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
        ];
    }
}
