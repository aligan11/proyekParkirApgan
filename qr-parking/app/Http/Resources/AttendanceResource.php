<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->attendance_date->format('Y-m-d'),
            'type' => $this->attendance_type,
            'check_in' => $this->when($this->check_in_time,[
                'time' => $this->check_in_time,
                'latitude' => $this->check_in_latitude,
                'longitude' => $this->check_in_longitude,
                'location' => $this->check_in_location,
                'photo_url' => $this->check_in_photo_url ? url('storage/'. $this->check_in_photo_url):null,
                'status' => $this->check_in_status,
            ]),
            'check_out' => $this->when($this->check_out_time,[
                'time' => $this->check_out_time,
                'latitude' => $this->check_out_latitude,
                'longitude' => $this->check_out_longitude,
                'location' => $this->check_out_location,
                'photo_url' => $this->check_out_photo_url ? url('storage/'. $this->check_out_photo_url):null,
                'status' => $this->check_out_status,
            ]),
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
