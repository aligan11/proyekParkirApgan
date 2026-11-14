<?php

namespace App\Http\Requests;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;

class topUpStoreRequest extends FormRequest
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
            'user_id' => 'required|int|min:1',
            'nominal' => 'required|numeric|min:1',
            'biaya_admin' => 'required|numeric|min:0',
            'total_dibayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'nullable|string|max:50',


        ];
    }
}
