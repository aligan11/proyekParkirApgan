<?php

namespace App\Http\Requests;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRuleRequest extends FormRequest
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
            'rule_name' => 'required|string|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'late_tolerance' => 'nullable|integer|min:0',
            'early_tolerance' => 'nullable|integer|min:0',
            'max_distance' => 'nullable|integer|min:0',
            'work_location_latitude' => 'required|numeric|between:-90,90',
            'work_location_longitude' => 'required|numeric|between:-180,180',
            'work_location_name' => 'required|string|max:255',
            'is_active' => 'sometimes|boolean'
        ];
    }
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse('Error Validations',422,$validator->errors());
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
