<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertProjectRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'client' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'cover_image' => ['nullable', 'url:http,https', 'max:2048'],
            'cover_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'status' => ['required', Rule::in(['active', 'in_review', 'completed'])],
            'is_favorite' => ['sometimes', 'boolean'],
            'fabrics' => ['sometimes', 'array', 'max:100'],
            'fabrics.*.name' => ['required_with:fabrics', 'string', 'max:255'],
            'fabrics.*.collection' => ['nullable', 'string', 'max:255'],
            'fabrics.*.color' => ['nullable', 'string', 'max:100'],
            'fabrics.*.image' => ['nullable', 'string', 'max:2048', 'regex:~^(https?://|/storage/)~'],
            'fabrics.*.availability' => ['nullable', 'string', 'max:100'],
            'fabrics.*.code' => ['nullable', 'string', 'max:100'],
            'fabrics.*.price' => ['nullable', 'numeric', 'min:0'],
            'saved_colors' => ['sometimes', 'array', 'max:100'],
            'saved_colors.*' => ['array:name,hex'],
            'saved_colors.*.name' => ['required', 'string', 'max:100'],
            'saved_colors.*.hex' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'palette' => ['sometimes', 'array', 'max:30'],
            'palette.*.name' => ['required_with:palette', 'string', 'max:100'],
            'palette.*.hex' => ['required_with:palette', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'notes' => ['sometimes', 'array', 'max:100'],
            'notes.*' => ['string', 'max:2000'],
            'inspiration_images' => ['sometimes', 'array', 'max:50'],
            'inspiration_images.*' => ['url:http,https', 'max:2048'],
            'members' => ['sometimes', 'array', 'max:50'],
            'members.*.name' => ['required_with:members', 'string', 'max:255'],
            'members.*.role' => ['nullable', 'string', 'max:100'],
            'members.*.initials' => ['nullable', 'string', 'max:3'],
            'timeline' => ['sometimes', 'array', 'max:100'],
            'timeline.*.title' => ['required_with:timeline', 'string', 'max:255'],
            'timeline.*.date' => ['nullable', 'date'],
            'timeline.*.completed' => ['sometimes', 'boolean'],
            'recent_activity' => ['sometimes', 'array', 'max:100'],
            'recent_activity.*.text' => ['required_with:recent_activity', 'string', 'max:500'],
            'recent_activity.*.time' => ['nullable', 'string', 'max:100'],
        ];
    }
}
