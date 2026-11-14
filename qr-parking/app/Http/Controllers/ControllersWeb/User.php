<?php


namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Kolom yang boleh diisi mass assignment
    protected $fillable = [
        'nama_lengkap',
        'photo',
        'jenis_kelamin',
        'no_hp',
        'username',
        'password',
        'alamat',
        'role', // misal: 'admin' atau 'user'
    ];

    // Kolom yang harus disembunyikan saat di-serialize (misal ketika mengubah ke JSON)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Kolom yang otomatis di-cast ke tipe data tertentu
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah user biasa
     */
    public function isUser()
    {
        return $this->role === 'user';
    }
}
