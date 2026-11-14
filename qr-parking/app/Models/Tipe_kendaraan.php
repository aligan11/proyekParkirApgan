<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Tipe_kendaraan extends Model
{
    use HasFactory;

    protected $table = 'tipe_kendaraan'; // pastikan sesuai nama tabel di database kamu

    protected $fillable = [
        'nama_kendaraan',
        'tarif_parkir',
        'keterangan_tarif',
    ];

    public function kendaraans()
    {
        return $this->hasMany(Kendaraan::class, 'id_jenis');
    }
    public function tarip()
    {
        return $this->hasMany(Kendaraan::class, 'id_tarif');
    }

    public function dataParkir()
    {
        return $this->hasMany(DataParkir::class, 'id_tarif');
    }

    /**
     * Relasi ke user
     */

    //
}
