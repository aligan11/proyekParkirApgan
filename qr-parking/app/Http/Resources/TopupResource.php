<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopupResource extends JsonResource
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
            'nama' => $this->nama,

            'nominal' => number_format($this->nominal, 0, ',', '.'),
            'biaya_admin' => number_format($this->biaya_admin, 0, ',', '.'),
            'total_bayar' => number_format($this->total_bayar, 0, ',', '.'),

            'deskripsi' => $this->deskripsi,

            'is_aktif' => $this->is_aktif,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
