<?php

namespace Database\Factories;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaksi>
 */
class TransaksiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_transaksi' => fake()->unique()->numerify('TRX-##########'),
            'karyawan_id' => Karyawan::factory(),
            'keterangan' => fake()->sentence(),
        ];
    }
}
