<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TypeGoodsRequest extends FormRequest
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
        $typeGoodsId = $this->route('type_goods')?->id;
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('type_goods', 'name')
                    ->ignore($typeGoodsId)
                    ->whereNull('deleted_at'),
            ],
            'description' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Тип товара с таким названием уже существует',
        ];
    }
}
