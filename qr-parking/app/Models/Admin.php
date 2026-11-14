<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // jika admin login
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admin'; // nama tabel, sesuaikan jika beda

    // Primary key tabel `admin` adalah id_admin (bukan id default Laravel)
    protected $primaryKey = 'id_admin';

    protected $fillable = [
        'id_admin',
        'nama_lengkap',
        'foto_admin',
        'jenis_kelamin',
        'no_hp',
        'username',
        'password',
        'alamat',
        'role',
    ];
}
