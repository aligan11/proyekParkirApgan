<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class kendaraanResource extends JsonResource
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
            'id_jenis' => $this->id_jenis,
            'plat_nomor' => $this->plat_nomor,
            'warna' => $this->warna,
            'user_id' => $this->user_id,
            'is_aktif' => $this->is_aktif,
        ];
    }
}
