<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatans = [
            ['nama_jabatan' => 'Ketua Koperasi', 'deskripsi' => 'Pimpinan tertinggi kepengurusan koperasi'],
            ['nama_jabatan' => 'Sekretaris', 'deskripsi' => 'Pengelola administrasi dan kesekretariatan koperasi'],
            ['nama_jabatan' => 'Bendahara', 'deskripsi' => 'Pengelola keuangan dan pembukuan koperasi'],
            ['nama_jabatan' => 'Pengawas', 'deskripsi' => 'Pengawas operasional dan tata kelola koperasi'],
            ['nama_jabatan' => 'Manajer Operasional', 'deskripsi' => 'Pengelola operasional toko dan warung koperasi'],
            ['nama_jabatan' => 'Staff Toko / Kasir', 'deskripsi' => 'Petugas pelayanan transaksi kasir dan stok toko'],
            ['nama_jabatan' => 'Staff', 'deskripsi' => 'Staff pelaksana divisi'],
            ['nama_jabatan' => 'Anggota', 'deskripsi' => 'Anggota aktif koperasi'],
        ];

        foreach ($jabatans as $item) {
            Jabatan::firstOrCreate(['nama_jabatan' => $item['nama_jabatan']], $item);
        }
    }
}
