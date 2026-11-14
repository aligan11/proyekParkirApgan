<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class isiSaldoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            'nominal' => number_format($this->nominal, 0, ',', '.'),
            'biaya_admin' => number_format($this->biaya_admin, 0, ',', '.'),
            'total_bayar' => number_format($this->total_bayar, 0, ',', '.'),


            'status' => $this->status,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
