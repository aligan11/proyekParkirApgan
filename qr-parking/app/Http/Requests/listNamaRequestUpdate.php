<?php

namespace App\Http\Requests;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class listNamaRequestUpdate extends FormRequest
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
            'nama_lengkap' => 'sometimes|string|max:50',
            'username' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
                Rule::unique('users', 'username')->ignore($this->route('id_users'), 'id_users'),
            ],
            'foto_users' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:10048',
            'password' => 'nullable|string|min:5|confirmed',
        ];
    }
}
