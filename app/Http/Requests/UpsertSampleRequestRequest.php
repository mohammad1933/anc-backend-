<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertSampleRequestRequest extends FormRequest
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
            'customer_id' => ['nullable', 'exists:customers,id'], 'company_name' => ['nullable', 'string', 'max:255'], 'industry' => ['nullable', 'string', 'max:100'],
            'full_name' => ['required', 'string', 'max:255'], 'country' => ['required', 'string', 'max:100'], 'delivery_address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:100'], 'email' => ['required', 'email', 'max:255'], 'phone' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'], 'status' => ['sometimes', Rule::in(['pending', 'approved', 'rejected', 'fulfilled'])],
            'items' => [$this->isMethod('post') ? 'required' : 'sometimes', 'array', 'min:1', 'max:3'],
            'items.*.catalog_id' => ['nullable', 'exists:catalogs,id'], 'items.*.color_id' => ['nullable', 'exists:colors,id'],
            'items.*.sample_name' => ['required', 'string', 'max:255'], 'items.*.quantity' => ['nullable', 'integer', 'min:1', 'max:3'],
        ];
    }
}
