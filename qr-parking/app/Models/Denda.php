<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denda extends Model
{
    use HasFactory;

    protected $table = 'denda';

    protected $fillable = [
        'id_users',
        'nama',
        'id_kendaraan',
        'plat_nomor',
        'jenis_pelanggaran',
        'waktu_pelanggaran',
        'denda',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'waktu_pelanggaran' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_users', 'id_users');
    }


    // Relasi ke Kendaraan
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan');
    }
}
