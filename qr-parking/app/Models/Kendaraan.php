<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';

    protected $fillable = [
        'id',
        'id_jenis',       // FK ke tipe_kendaraan
        'merk_kendaraan',
        'plat_nomor',
        'warna',
        'user_id',        // FK ke user
        'is_aktif',
        'id_tarif',       // FK ke tipe_kendaraan (tarif)
    ];

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke tipe kendaraan (jenis)
     */
    public function jenis()
    {
        return $this->belongsTo(Tipe_kendaraan::class, 'id_jenis');
    }

    /**
     * Relasi tarif
     */
    public function tarif()
    {
        return $this->belongsTo(Tipe_kendaraan::class, 'id_tarif');
    }

    /**
     * Relasi denda
     */
    public function denda()
    {
        return $this->hasMany(Denda::class, 'id_kendaraan');
    }

    /**
     * Relasi DataParkir jika ingin ambil riwayat parkir
     */
    public function dataParkir()
    {
        return $this->hasMany(DataParkir::class, 'id_plat_nomor', 'id');
    }
}
