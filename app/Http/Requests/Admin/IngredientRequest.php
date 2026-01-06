<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IngredientRequest extends FormRequest
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
        return [
            'recipe_id' => [
                'required',
                'numeric',
                'exists:recipes,id'
            ],
            'product_id' => [
                'required',
                'numeric',
                'exists:products,id',
                Rule::exists('products', 'id')->whereNull('deleted_at'),
            ],
            'unit_id' => [
                'required',
                'numeric',
                'exists:units,id',
                Rule::exists('units', 'id')->whereNull('deleted_at'),
            ],
            'quantity' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
            ],
            'notes' => [
                'nullable',
                'string'
            ],
            'is_optional' => [
                'boolean'
            ],
            'sort_order' => [
                'integer',
                'min:0',
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'recipe_id.exists' => 'Указанный рецепт не существует',
            'product_id.exists' => 'Указанный продукт не существует',
            'unit_id.exists' => 'Указанная единица измерения не существует',
        ];
    }

    /**
     * Подготовка данных перед валидацией
     * @return void
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'is_optional' => $this->boolean('is_optional'),
            'sort_order' => $this->input('sort_order', 0),
        ]);
    }
}
