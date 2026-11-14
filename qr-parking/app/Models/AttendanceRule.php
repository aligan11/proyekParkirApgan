<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_name',
        'start_time',
        'end_time',
        'late_tolerance',
        'early_tolerance',
        'max_distance',
        'work_location_latitude',
        'work_location_longitude',
        'work_location_name',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];
}
