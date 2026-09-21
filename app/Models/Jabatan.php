<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatans';

    protected $fillable = [
        'nama_jabatan',
        'deskripsi',
    ];

    // 1 jabatan dimiliki oleh banyak karyawan
    public function karyawans(): HasMany
    {
        return $this->hasMany(Karyawan::class, 'jabatan_id');
    }

    // 1 jabatan dimiliki oleh banyak user
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'jabatan_id');
    }
}
