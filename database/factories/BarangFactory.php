<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Barang>
 */
class BarangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_barang' => fake()->word(),
            'harga_jual' => fake()->numberBetween(1000, 100000),
            'stok' => fake()->numberBetween(0, 100),
            'kategori_id' => Kategori::factory(),
            'deskripsi' => fake()->sentence(),
        ];
    }
}
