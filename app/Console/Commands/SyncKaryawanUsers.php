<?php

namespace App\Console\Commands;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SyncKaryawanUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:sync-karyawan
                            {--force-password : Reset password seluruh karyawan ke default NIP}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi akun User untuk seluruh data Karyawan (Password default NIP, Username nama karyawan lengkap) dan buat akun Admin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('=== Sinkronisasi Akun Pengguna Warung Koperasi ===');
        $this->newLine();

        // 1. Akun Admin Default
        $admin = User::firstOrNew(['username' => 'admin']);
        $admin->name = 'Administrator';
        $admin->email = 'admin@koperasi.com';
        $admin->role = 'admin';
        if (!$admin->exists || $this->option('force-password')) {
            $admin->password = Hash::make('admin123');
        }
        $admin->save();
        $this->line('  <fg=green>OK</> Akun Admin siap: username=<comment>admin</comment>, password=<comment>admin123</comment>');

        // Update user bawaan sebelumnya jika ada
        User::where('email', 'test@example.com')->whereNull('karyawan_id')->update([
            'role' => 'admin',
        ]);

        // 2. Loop Karyawan
        $karyawans = Karyawan::all();
        $createdCount = 0;
        $updatedCount = 0;
        $forcePass = $this->option('force-password');

        foreach ($karyawans as $karyawan) {
            $user = User::where('karyawan_id', $karyawan->id)->first();
            $isNew = false;

            if (!$user) {
                // Cek apakah ada user lama dengan email yang sama
                if ($karyawan->email) {
                    $user = User::where('email', $karyawan->email)->first();
                }
                if (!$user) {
                    $user = new User();
                    $isNew = true;
                }
            }

            $user->name = $karyawan->nama_karyawan;
            $user->karyawan_id = $karyawan->id;
            $user->role = 'karyawan';
            $user->jabatan_id = $karyawan->jabatan_id;

            // Username default nama karyawan lengkap
            $cleanUsername = trim($karyawan->nama_karyawan);
            // Cek keunikan username terhadap user lain
            $existingWithSameUsername = User::where('username', $cleanUsername)
                ->where('id', '!=', $user->id ?? 0)
                ->exists();

            if ($existingWithSameUsername && $karyawan->nip) {
                $user->username = $cleanUsername . ' (' . $karyawan->nip . ')';
            } else {
                $user->username = $cleanUsername;
            }

            // Email fallback jika kosong
            if ($karyawan->email) {
                $user->email = $karyawan->email;
            } elseif (!$user->email) {
                $slug = Str::slug($karyawan->nama_karyawan, '_');
                $user->email = ($slug ?: 'karyawan') . '_' . $karyawan->id . '@koperasi.local';
            }

            // Password default menggunakan NIP (atau fallback 123456 jika NIP kosong)
            $defaultPassword = $karyawan->nip ?: '123456';
            if ($isNew || $forcePass || !$user->password) {
                $user->password = Hash::make($defaultPassword);
            }

            $user->save();

            if ($isNew) {
                $createdCount++;
            } else {
                $updatedCount++;
            }
        }

        $this->newLine();
        $this->info("Sinkronisasi Selesai:");
        $this->line("  - Akun Baru Dibuat : <fg=green>{$createdCount}</>");
        $this->line("  - Akun Diperbarui  : <fg=cyan>{$updatedCount}</>");
        $this->line("  - Total Karyawan   : <fg=yellow>{$karyawans->count()}</>");
        $this->newLine();
        $this->comment("Catatan: Karyawan dapat login menggunakan Nama Lengkap atau NIP mereka, dengan password default NIP masing-masing.");

        return self::SUCCESS;
    }
}
