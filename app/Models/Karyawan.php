<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Karyawan extends Model
{
    protected $table = 'karyawans';

    protected $fillable = [
        'nip',
        'nama_karyawan',
        'alamat',
        'departemen_id',
        'no_hp',
        'email',
    ];

    // karyawan milik 1 departemen
    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemens::class, 'departemen_id');
    }

    // 1 karyawan punya banyak transaksi
    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }
}