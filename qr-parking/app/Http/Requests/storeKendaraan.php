<?php

namespace App\Http\Requests;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;

class storeKendaraan extends FormRequest
{
    use ApiResponse;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_jenis' => 'required|int|min:1',
            'merk_kendaraan' => 'required|string|max:50',
            'plat_nomor' => 'required|string|max:50',
            'warna' => 'required|string|max:50',
            'user_id' => 'required|int|min:1',
        ];
    }
}
