<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TambahStok extends Model
{
    protected $table = 'tambah_stoks';

    protected $fillable = [
        'barang_id',
        'jumlah',
        'tanggal',
        'keterangan',
    ];

    // konversi tanggal string jadi object Carbon
    protected $casts = [
        'tanggal' => 'date',
    ];

    // stok masuk untuk 1 barang
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}