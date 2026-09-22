<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        $this->call([
            JabatanSeeder::class,
            KategoriSeeder::class,
            BarangSeeder::class,
            KaryawanSeeder::class,
            TransaksiSeeder::class,
        ]);

        \Illuminate\Support\Facades\Artisan::call('users:sync-karyawan');
    }
}
