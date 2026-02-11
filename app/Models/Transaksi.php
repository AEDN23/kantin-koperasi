<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    protected $table = 'transaksis';

    protected $fillable = [
        'kode_transaksi',
        'karyawan_id',
        'keterangan',
    ];

    // transaksi milik 1 karyawan
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    // 1 transaksi punya banyak detail (item yang dibeli)
    public function transaksiDetails(): HasMany
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    // Hitung total belanja otomatis dari semua detail
    public function getTotalBelanjaAttribute(): int
    {
        return $this->transaksiDetails->sum('total_harga');
    }
}