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
        $departemens = [
            ['nama_departemen' => 'Keuangan', 'deskripsi' => 'Divisi Keuangan dan Accounting'],
            ['nama_departemen' => 'SDM', 'deskripsi' => 'Sumber Daya Manusia'],
            ['nama_departemen' => 'Operasional', 'deskripsi' => 'Divisi Operasional Toko'],
            ['nama_departemen' => 'IT', 'deskripsi' => 'Teknologi Informasi'],
        ];

        foreach ($departemens as $dept) {
            Departemens::updateOrCreate(['nama_departemen' => $dept['nama_departemen']], $dept);
        }

        $deptKeuangan = Departemens::where('nama_departemen', 'Keuangan')->first();
        $deptSDM = Departemens::where('nama_departemen', 'SDM')->first();
        $deptOps = Departemens::where('nama_departemen', 'Operasional')->first();
        $deptIT = Departemens::where('nama_departemen', 'IT')->first();

        $karyawans = [
            [
                'nip' => '1001',
                'nama_karyawan' => 'Andi Setiawan',
                'alamat' => 'Jl. Merdeka No. 10',
                'departemen_id' => $deptKeuangan->id,
                'no_hp' => '081234567890',
                'email' => 'andi@example.com',
            ],
            [
                'nip' => '1002',
                'nama_karyawan' => 'Budi Santoso',
                'alamat' => 'Jl. Mawar No. 5',
                'departemen_id' => $deptSDM->id,
                'no_hp' => '081234567891',
                'email' => 'budi@example.com',
            ],
            [
                'nip' => '1003',
                'nama_karyawan' => 'Citra Lestari',
                'alamat' => 'Jl. Melati No. 8',
                'departemen_id' => $deptOps->id,
                'no_hp' => '081234567892',
                'email' => 'citra@example.com',
            ],
            [
                'nip' => '1004',
                'nama_karyawan' => 'Dedi Kurniawan',
                'alamat' => 'Jl. Anggrek No. 12',
                'departemen_id' => $deptIT->id,
                'no_hp' => '081234567893',
                'email' => 'dedi@example.com',
            ],
            [
                'nip' => '1005',
                'nama_karyawan' => 'Eka Putri',
                'alamat' => 'Jl. Tulip No. 3',
                'departemen_id' => $deptOps->id,
                'no_hp' => '081234567894',
                'email' => 'eka@example.com',
            ],
        ];

        foreach ($karyawans as $karyawan) {
            Karyawan::updateOrCreate(['nip' => $karyawan['nip']], $karyawan);
        }
    }
}
