<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Karyawan extends Model
{
    use HasFactory;
    protected $table = 'karyawans';

    protected $fillable = [
        'nip',
        'nama_karyawan',
        'alamat',
        'departemen_id',
        'jabatan_id',
        'no_hp',
        'email',
    ];

    // karyawan milik 1 departemen
    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemens::class, 'departemen_id');
    }

    // karyawan memiliki 1 jabatan
    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    // 1 karyawan punya banyak transaksi
    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }

    public function getTotalTransaksiAttribute(): int
    {
        if (array_key_exists('total_transaksi', $this->attributes)) {
            return (int) $this->attributes['total_transaksi'];
        }
        return (int) TransaksiDetail::whereHas('transaksi', fn($q) => $q->where('karyawan_id', $this->id))->sum('total_harga');
    }

    public function getTotalPiutangAttribute(): int
    {
        if (array_key_exists('total_piutang', $this->attributes)) {
            return (int) $this->attributes['total_piutang'];
        }
        return (int) TransaksiDetail::whereHas('transaksi', fn($q) => $q->where('karyawan_id', $this->id))
            ->where('metode_pembayaran', 'piutang')
            ->where('status_pembayaran', 'belum_lunas')
            ->sum('total_harga');
    }
}