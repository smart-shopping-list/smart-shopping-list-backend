<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnitRequest extends FormRequest
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
        $unitId = $this->route('unit')?->id;
        return [
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('units', 'name')
                    ->ignore($unitId)
                    ->whereNull('deleted_at'),
            ],
            'short_name' => [
                'required',
                'string',
                'max:5',
                Rule::unique('units', 'short_name')
                    ->ignore($unitId)
                    ->whereNull('deleted_at'),
            ],
            'type' => [
                'required',
                'string',
                Rule::in(['weight', 'volume', 'count', 'length']),
            ],
            'conversion_rate' => [
                'required',
                'numeric',
                'min:0.0001',
                'max:999999.9999',
            ],
            'is_default' => [
                'boolean',
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
            'name.unique' => 'Такая единица измерения уже существует',
            'short_name.unique' => 'Такое сокращение уже используется',
            'conversion_rate.min' => 'Коэффициент конвертации должен быть больше 0',
        ];
    }

    /**
     * Подготовка данных перед валидацией
     * @return void
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'is_default' => $this->boolean('is_default'),
            'sort_order' => $this->input('sort_order', 0),
        ]);
    }
}
