<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class SignupRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            "name" => "required|string|max:55",
            "email" => "required|email|unique:users,email",
            "password" => [
                "required",
                "confirmed",
                Password::min(8)->letters()->numbers(),
                // Password::min(8)->letters()->symbols()->numbers(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            "password" =>
                "Пароль должен содержать минимум 8 символов, включая буквы, цифры и символы.",
        ];
    }
}
