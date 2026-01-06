<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecipeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $recipeId = $this->route('recipe')?->id;
        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('recipes', 'name')
                    ->ignore($recipeId)
                    ->whereNull('deleted_at'),
            ],
            'type' => [
                'required',
                'string',
                Rule::in(['food', 'drink', 'other']),
            ],
            'cooking_time' => [
                'nullable',
                'integer',
                'min:1', // Минимум 1 минута
                'max:1440', // Максимум 24 часа
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'servings' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
            'instructions' => [
                'nullable',
                'string',
            ],

            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
            'source_url' => [
                'nullable',
                'string',
                'max:150',
                'url',
            ],
            'is_public' => [
                'boolean',
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'Рецепт с таким названием уже существует',
            'cooking_time.max' => 'Время приготовления не может превышать 24 часа',
            'servings.max' => 'Количество порций не может превышать 100',
        ];
    }

    /**
     * @return void
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'is_public' => $this->boolean('is_public'),
            'cooking_time' => $this->input('cooking_time') ?: null,
            'servings' => $this->input('servings', 1),
        ]);
    }
}
