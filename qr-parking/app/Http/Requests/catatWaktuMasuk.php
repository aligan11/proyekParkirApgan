<?php

namespace App\Http\Requests;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;

class catatWaktuMasuk extends FormRequest
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
            'user_id' => 'required|integer|min:1',
            'kode_parkir' => 'required|string|max:100|unique:dataparkir,kode_parkir',


            'id_merk_kendaraan' => 'required|integer|min:1',
            'id_plat_nomor' => 'required|integer|min:1',
            'id_tipe_kendaraan' => 'required|integer|min:1',
            'id_warna_kendaraan' => 'required|integer|min:1',
            'id_tarif' => 'required|integer|min:1',
            'lokasi' => 'nullable|string|max:200',
            'tempat' => 'nullable|string|max:200',
            'status' => 'nullable|string|max:100',
        ];
    }
}
