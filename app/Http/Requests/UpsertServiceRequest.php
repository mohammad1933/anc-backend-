<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertServiceRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'], 'slug' => ['required', 'string', 'max:255', Rule::unique('services')->ignore($this->route('service'))],
            'type' => ['nullable', 'string', 'max:100'], 'description' => ['required', 'string'], 'tags' => ['nullable', 'array'], 'tags.*' => ['string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], 'cta_label' => ['nullable', 'string', 'max:100'], 'cta_url' => ['nullable', 'string', 'max:2048'],
            'status' => ['required', Rule::in(['draft', 'visible', 'hidden'])], 'sort_order' => ['integer', 'min:0'],
        ];
    }
}
