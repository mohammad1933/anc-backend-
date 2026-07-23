<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertInquiryRequest extends FormRequest
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
            'customer_id' => ['nullable', 'exists:customers,id'], 'service_id' => ['nullable', 'exists:services,id'], 'catalog_id' => ['nullable', 'exists:catalogs,id'],
            'color_id' => ['nullable', 'exists:colors,id'], 'full_name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'], 'company_name' => ['nullable', 'string', 'max:255'], 'department' => ['nullable', 'string', 'max:100'],
            'subject' => ['nullable', 'string', 'max:255'], 'message' => ['required', 'string', 'max:5000'],
            'status' => ['sometimes', Rule::in(['new', 'in_progress', 'responded', 'closed'])],
        ];
    }
}
