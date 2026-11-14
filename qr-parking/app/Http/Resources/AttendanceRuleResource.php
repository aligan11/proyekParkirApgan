<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AttendanceRuleResource extends JsonResource
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
            'rule_name' => $this->rule_name,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'late_tolerance' => $this->late_tolerance,
            'early_tolerance' => $this->early_tolerance,
            'max_distance' => $this->max_distance,
            'work_location' => [
                'latitude' => $this->work_location_latitude,
                'longitude' => $this->work_location_longitude,
                'name' => $this->work_location_name,
            ],
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
