<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketTopup extends Model
{
    use HasFactory;

    protected $table = 'paket_topups'; // Sesuaikan jika nama tabel beda

    protected $fillable = [
        'nama',          // varchar(100)
        'nominal',       // decimal(12,2)
        'biaya_admin',   // decimal(12,2), nullable, default 0.00
        'total_bayar',   // decimal(12,2)
        'deskripsi',     // text, nullable
        'is_aktif',      // boolean atau tinyint
    ];

    protected $casts = [
        'biaya_admin' => 'float',
        'nominal' => 'float',
        'total_bayar' => 'float',
        'is_aktif' => 'boolean',
    ];
}
