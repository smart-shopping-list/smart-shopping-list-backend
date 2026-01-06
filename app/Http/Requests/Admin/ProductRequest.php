<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
        $productId = $this->route('product')?->id;
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'name')
                    ->ignore($productId)
                    ->whereNull('deleted_at'),
            ],

            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
            'type_good_id' => [
                'required',
                'integer',
                'exists:type_goods,id',
            ],
            'default_unit_id' => [
                'required',
                'integer',
                'exists:units,id',
            ],
            'package_unit_id' => [
                'nullable',
                'integer',
                'exists:units,id',
            ],
            'package_amount' => [
                'nullable',
                'numeric',
                'min:0.01',
                'max:999999.99',
            ],
            'package_size' => [
                'nullable',
                'string',
                'max:50',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'alternative_names' => [
                'nullable',
                'array',
            ],
            'alternative_names.*' => [
                'string',
                'max:50',
            ],
            'is_active' => [
                'boolean',
            ],

            'calories_per_100' => [
                'nullable',
                'integer',
                'min:0',
                'max:10000',
            ],
            'protein_per_100' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
            ],
            'fat_per_100' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
            ],
            'carbs_per_100' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
            ],
            'sugar_per_100' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
            ],
            'fiber_per_100' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
            ],
            'salt_per_100' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
            ],
            'cholesterol_per_100' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
            ],
            'vitamin_c_per_100' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
            ],
            'iron_per_100' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
            ],
            'calcium_per_100' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'Товар с таким названием уже существует',
            'category_id.exists' => 'Указанная категория не существует',
            'type_good_id.exists' => 'Указанный тип товара не существует',
            'default_unit_id.exists' => 'Указанная единица измерения не существует',
            'package_unit_id.exists' => 'Указанная единица измерения упаковки не существует',
        ];
    }

    /**
     * @return void
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'alternative_names' => $this->input('alternative_names', []),
        ]);
    }
}
