<?php

namespace App\Http\Requests;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;

class listNamaRequest extends FormRequest
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
            'nama' => 'required|string|max:50',
            'no_hp' => 'required|string|max:50',
            'username' => 'required|string|max:100|unique:users',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:10048', // max 2MB
            'password' => 'required|string|min:5|confirmed',


        ];
    }
}
