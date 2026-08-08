<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpsertCatalogRequest extends FormRequest
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
            'category_id' => ['nullable', 'exists:categories,id'], 'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('catalogs')->ignore($this->route('catalog'))],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('catalogs')->ignore($this->route('catalog'))],
            'description' => ['nullable', 'string'], 'material' => ['nullable', 'string', 'max:100'], 'composition' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0.01', 'max:99999999.99'], 'currency' => ['nullable', 'string', 'size:3'],
            'discount_percent' => ['nullable', 'integer', 'between:1,99'], 'discount_starts_at' => ['nullable', 'date'],
            'discount_ends_at' => ['nullable', 'date', 'after:discount_starts_at'],
            'applications' => ['nullable', 'array'], 'applications.*' => ['string', 'max:100'], 'specifications' => ['nullable', 'array'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], 'pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'status' => ['required', Rule::in(['draft', 'published', 'hidden'])], 'is_featured' => ['boolean'], 'is_new' => ['boolean'],
        ];
    }

    /** @return array<callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($this->filled('discount_percent') && ! $this->filled('price')) {
                $validator->errors()->add('price', 'A regular price is required when a discount is set.');
            }
        }];
    }
}
