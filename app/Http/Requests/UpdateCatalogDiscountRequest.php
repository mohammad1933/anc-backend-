<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCatalogDiscountRequest extends FormRequest
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
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'enabled' => ['required', 'boolean'],
            'price' => ['required_if:enabled,true', 'nullable', 'numeric', 'min:0.01', 'max:99999999.99'],
            'currency' => ['required_if:enabled,true', 'nullable', 'string', 'size:3'],
            'discount_percent' => ['required_if:enabled,true', 'nullable', 'integer', 'between:1,99'],
            'discount_starts_at' => ['nullable', 'date'],
            'discount_ends_at' => ['nullable', 'date', 'after:discount_starts_at'],
        ];
    }
}
