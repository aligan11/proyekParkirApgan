<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParkirRequest extends FormRequest
{
    public function authorize()
    {
        return true; // atau sesuaikan jika ingin pakai otorisasi
    }

    public function rules()
    {
        return [
            'waktu_keluar'      => 'nullable|date',
            'total'             => 'nullable|string',
        ];
    }
}
