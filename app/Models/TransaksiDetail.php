<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiDetail extends Model
{
    use HasFactory;
    protected $table = 'transaksi_details';

    protected $fillable = [
        'transaksi_id',
        'barang_id',
        'jumlah',
        'harga_satuan',
        'total_harga',
    ];

    // detail milik 1 transaksi
    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class);
    }

    // detail mereferensikan 1 barang
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}