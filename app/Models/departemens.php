<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departemens extends Model
{
    protected $table = 'departemens';

    protected $fillable = [
        'nama_departemen',
        'deskripsi',
    ];

    // 1 departemen punya banyak karyawan
    public function karyawans(): HasMany
    {
        return $this->hasMany(Karyawan::class);
    }
}