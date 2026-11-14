<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';
    protected $fillable = [
        'user_id',
        'attendance_date',
        'attendance_type',
        'check_in_time',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_location',
        'check_in_photo_url',
        'check_in_status',
        'check_out_time',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_location',
        'check_out_photo_url',
        'check_out_status',
        'reason',
        'status',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
