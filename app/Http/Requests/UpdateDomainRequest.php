<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDomainRequest extends FormRequest
{
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'company_name' => 'sometimes|nullable|string|max:255',
            'booking_date' => 'sometimes|required|date',
            'expiry_date' => 'sometimes|required|date|after_or_equal:booking_date',
            'sales_person_name' => 'sometimes|required|string|max:255',
            'status_id' => 'sometimes|nullable|exists:statuses,id',
            'remark' => 'sometimes|nullable|string',
        ];
    }
}
