<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyAplikasi extends Model
{
    protected $table = 'propertyaplikasi';
    // Tambahkan ini jika tidak ada kolom `id`
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nama_aplikasi',
        'kode_parkir',
        'logo_aplikasi',
        'owner_parkir',
        'alamat_perusahaan',
        'email_perusahaan',
        'no_perusahaan',
    ];
}
