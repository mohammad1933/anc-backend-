<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFavoriteRequest extends FormRequest
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
            'favorite_folder_id' => ['sometimes', 'nullable', Rule::exists('favorite_folders', 'id')->where('user_id', $this->user()->id)],
            'type' => ['sometimes', Rule::in(['collection', 'color', 'texture'])],
            'name' => ['sometimes', 'string', 'max:255'],
            'collection' => ['sometimes', 'string', 'max:255'],
            'material' => ['sometimes', 'string', 'max:255'],
            'image_url' => ['sometimes', 'url:http,https', 'max:2048'],
            'colors' => ['sometimes', 'array', 'max:30'],
            'colors.*' => ['regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }
}
