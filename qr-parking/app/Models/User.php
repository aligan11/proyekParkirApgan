<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id_users';
    public $incrementing = true;
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_lengkap',
        'foto_users',
        'jenis_kelamin',
        'no_hp',
        'username',
        'password',
        'alamat',
        'role',
        'saldo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function denda()
    {
        return $this->hasMany(Denda::class, 'id_users');
    }


    public function isStaff()
    {
        return $this->role === 'staff';
    }


    // Di model User.php
    public function dataParkir()
    {
        return $this->hasMany(DataParkir::class, 'user_id', 'id_users');
    }

    public function topUp()
    {
        return $this->hasMany(Topup::class, 'user_id', 'id_users');
    }
}
