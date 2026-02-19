<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use HasFactory;
    protected $table = 'kategoris';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    // 1 kategori punya banyak barang
    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class);
    }
}