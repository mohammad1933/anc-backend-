<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFavoriteRequest extends FormRequest
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
            'favorite_folder_id' => ['nullable', Rule::exists('favorite_folders', 'id')->where('user_id', $this->user()->id)],
            'catalog_id' => ['nullable', 'exists:catalogs,id'],
            'type' => ['required', Rule::in(['collection', 'color', 'texture'])],
            'name' => ['required', 'string', 'max:255'],
            'collection' => ['required', 'string', 'max:255'],
            'material' => ['required', 'string', 'max:255'],
            'image_url' => ['required', 'url:http,https', 'max:2048'],
            'colors' => ['sometimes', 'array', 'max:30'],
            'colors.*' => ['regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }
}
