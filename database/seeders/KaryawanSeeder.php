<?php

namespace Database\Seeders;

use App\Models\Departemens;
use App\Models\Karyawan;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada departemen sebelum membuat karyawan
        if (Departemens::count() === 0) {
            Departemens::factory()->count(5)->create();
        }

        Karyawan::factory()->count(15)->create();
    }
}
