<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetPostgresSequences extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:reset-sequences
                            {--table= : Reset sequence for a specific table only}';

    /**
     * The console command description.
     */
    protected $description = 'Reset PostgreSQL auto-increment sequences to sync with existing data (fixes Unique violation on primary key)';

    /**
     * Tables yang perlu di-reset sequence-nya.
     * Tambahkan tabel baru di sini jika diperlukan.
     */
    protected array $tables = [
        'jabatans',
        'departemens',
        'karyawans',
        'users',
        'barangs',
        'transaksis',
        'transaksi_details',
    ];

    public function handle(): int
    {
        if (config('database.default') !== 'pgsql') {
            $this->warn('Perintah ini hanya berlaku untuk koneksi PostgreSQL.');
            return self::FAILURE;
        }

        $targetTable = $this->option('table');
        $tables = $targetTable ? [$targetTable] : $this->tables;

        $this->info('Mereset PostgreSQL sequences...');
        $this->newLine();

        $success = 0;
        $failed  = 0;

        foreach ($tables as $table) {
            try {
                // Cek apakah tabel ada
                $exists = DB::select(
                    "SELECT to_regclass('public.{$table}') AS tbl"
                );

                if (empty($exists) || $exists[0]->tbl === null) {
                    $this->line("  <fg=yellow>SKIP</> {$table} — tabel tidak ditemukan");
                    continue;
                }

                // Ambil max id
                $maxId = DB::table($table)->max('id');

                if ($maxId === null) {
                    $this->line("  <fg=cyan>SKIP</> {$table} — tabel kosong, sequence dibiarkan");
                    continue;
                }

                // Reset sequence ke nilai max id
                DB::statement(
                    "SELECT setval(pg_get_serial_sequence('{$table}', 'id'), {$maxId})"
                );

                $this->line("  <fg=green>OK</>    {$table} — sequence direset ke {$maxId}");
                $success++;
            } catch (\Throwable $e) {
                $this->line("  <fg=red>ERROR</> {$table} — " . $e->getMessage());
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Selesai: {$success} berhasil, {$failed} gagal.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
