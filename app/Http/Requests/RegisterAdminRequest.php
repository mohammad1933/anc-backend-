<?php

namespace App\Http\Requests;

use App\Models\User;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterAdminRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique((new User)->getTable(), 'email')],
            'password' => ['required', 'confirmed', Password::min(10)->letters()->mixedCase()->numbers()->symbols()],
            'registration_key' => [
                'required',
                'string',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $configuredKey = config('services.admin_registration_key');

                    if (! is_string($configuredKey) || $configuredKey === '' || ! hash_equals($configuredKey, (string) $value)) {
                        $fail('The admin registration key is invalid.');
                    }
                },
            ],
        ];
    }
}
