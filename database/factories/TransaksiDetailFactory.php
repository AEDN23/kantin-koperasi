<?php

namespace Database\Factories;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransaksiDetail>
 */
class TransaksiDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $barang = Barang::factory()->create();
        $jumlah = fake()->numberBetween(1, 10);
        $harga_satuan = $barang->harga_jual;

        return [
            'transaksi_id' => Transaksi::factory(),
            'barang_id' => $barang->id,
            'jumlah' => $jumlah,
            'harga_satuan' => $harga_satuan,
            'total_harga' => $jumlah * $harga_satuan,
        ];
    }
}
