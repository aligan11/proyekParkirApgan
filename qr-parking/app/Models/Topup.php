<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Topup extends Model
{
    protected $table = 'topups'; // Sesuaikan jika nama tabel beda

    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'nominal',
        'biaya_admin',
        'total_dibayar',
        'metode_pembayaran',
        'status',
        'midtrans_snap_token',
        'midtrans_redirect_url',
        'transaction_details',
    ];

    protected $casts = [
        'transaction_details' => 'array',
    ];

    // Topup.php
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_users');
    }
}
