<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertColorRequest extends FormRequest
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
            'catalog_id' => ['required', 'exists:catalogs,id'], 'name' => ['required', 'string', 'max:255'], 'code' => ['required', 'string', 'max:100'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('colors')->ignore($this->route('color'))],
            'type' => ['required', Rule::in(['plain', 'pattern'])],
            'color_family' => ['nullable', 'string', 'max:100'], 'price' => ['nullable', 'numeric', 'min:0'], 'currency' => ['required', 'string', 'size:3'],
            'stock_quantity' => ['integer', 'min:0'], 'stock_status' => ['required', Rule::in(['in_stock', 'low_stock', 'out_of_stock', 'check_stock'])],
            'swatch' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], 'is_active' => ['boolean'],
        ];
    }
}
