<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataParkir extends Model
{
    protected $table = 'dataparkir';
    protected $primaryKey = 'id_parkir';
    public $timestamps = false; // nonaktifkan jika tidak pakai created_at dan updated_at

    protected $fillable = [
        'user_id',
        'order_id',
        'kode_parkir',
        'id_merk_kendaraan',
        'id_plat_nomor',
        'id_tipe_kendaraan',
        'id_warna_kendaraan',
        'waktu_masuk',
        'waktu_keluar',
        'id_tarif',
        'total',
        'lokasi',
        'tempat',
        'status',
        'fee_admin',
    ];

    // Relasi contoh:
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_users');
    }

    public function merkKendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_merk_kendaraan');
    }

    public function platNomor()
    {
        return $this->belongsTo(Kendaraan::class, 'id_plat_nomor');
    }

    public function tipeKendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_tipe_kendaraan');
    }

    public function warnaKendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_warna_kendaraan');
    }

    public function tarif()
    {
        return $this->belongsTo(Tipe_kendaraan::class, 'id_tarif');
    }
}
