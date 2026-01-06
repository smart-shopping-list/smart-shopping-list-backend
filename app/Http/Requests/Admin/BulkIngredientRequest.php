<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    public function rules(): array
    {
        return [
            // Массив ингредиентов (минимум 1)
            'ingredients' => 'required|array|min:1|max:50', // Защита от слишком больших запросов

            // Правила для каждого элемента массива:
            'ingredients.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
                // Проверяем, что продукт не удален
                Rule::exists('products', 'id')->whereNull('deleted_at'),
            ],

            'ingredients.*.unit_id' => [
                'required',
                'integer',
                'exists:units,id',
                Rule::exists('units', 'id')->whereNull('deleted_at'),
            ],

            'ingredients.*.quantity' => [
                'required',
                'numeric',
                'min:0.001', // Минимум 0.001 (для специй и т.д.)
                'max:999999.999',
            ],

            'ingredients.*.notes' => [
                'nullable',
                'string',
                'max:255',
            ],

            'ingredients.*.is_optional' => [
                'boolean',
            ],

            'ingredients.*.sort_order' => [
                'integer',
                'min:0',
                'max:999',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ingredients.required' => 'Необходимо указать хотя бы один ингредиент',
            'ingredients.*.product_id.required' => 'Укажите продукт для каждого ингредиента',
            'ingredients.*.product_id.exists' => 'Продукт с ID :value не существует',
            'ingredients.*.quantity.min' => 'Количество должно быть больше 0',
        ];
    }

    /**
     * @return void
     */
    public function prepareForValidation(): void
    {
        // Обработка данных перед валидацией
        $ingredients = $this->input('ingredients', []);

        foreach ($ingredients as &$ingredient) {
            $ingredient['is_optional'] = $this->booleanValue($ingredient['is_optional'] ?? false);
            $ingredient['sort_order'] = (int) ($ingredient['sort_order'] ?? 0);
        }

        $this->merge(['ingredients' => $ingredients]);
    }

    /**
     * @param $value
     * @return bool
     */
    private function booleanValue($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
