<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $menuId = $this->route('menu')?->id;
        return [
            'start_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                // Проверяем, что это понедельник
                function ($attribute, $value, $fail) {
                    if (!\Carbon\Carbon::parse($value)->isMonday()) {
                        $fail('Дата начала должна быть понедельником');
                    }
                },
                // Уникальность для пользователя (одно меню на неделю)
                Rule::unique('menus', 'start_date')
                    ->where('user_id', $this->user()->id)
                    ->whereNull('deleted_at')
                    ->ignore($menuId),
            ],

            'end_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:start_date',
                // Проверяем, что это воскресенье
                function ($attribute, $value, $fail) {
                    if (!\Carbon\Carbon::parse($value)->isSunday()) {
                        $fail('Дата окончания должна быть воскресеньем');
                    }
                },
                // Проверяем разницу в 6 дней
                function ($attribute, $value, $fail) {
                    $startDate = \Carbon\Carbon::parse($this->start_date);
                    $endDate = \Carbon\Carbon::parse($value);

                    if ($startDate->diffInDays($endDate) !== 6) {
                        $fail('Период должен составлять ровно 7 дней (неделя)');
                    }
                },
            ],

            'name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'is_active' => [
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.unique' => 'У вас уже есть меню на эту неделю',
            'end_date.after_or_equal' => 'Дата окончания должна быть позже или равна дате начала',
        ];
    }

    public function prepareForValidation(): void
    {
        // Автоматически вычисляем end_date, если не указан
        if ($this->has('start_date') && !$this->has('end_date')) {
            $startDate = \Carbon\Carbon::parse($this->start_date);
            $this->merge([
                'end_date' => $startDate->copy()->addDays(6)->format('Y-m-d'),
            ]);
        }

        // Автоматически генерируем имя, если не указано
        if (!$this->has('name') && $this->has('start_date')) {
            $startDate = \Carbon\Carbon::parse($this->start_date);
            $endDate = \Carbon\Carbon::parse($this->end_date ?? $startDate->copy()->addDays(6));

            $this->merge([
                'name' => "Меню на неделю {$startDate->format('d.m')}-{$endDate->format('d.m')}",
            ]);
        }

        $this->merge([
            'is_active' => $this->boolean('is_active', false),
        ]);
    }

    /**
     * Get data to be validated from the request.
     */
    public function validationData(): array
    {
        $data = parent::validationData();

        // Добавляем user_id из текущего пользователя
        $data['user_id'] = $this->user()->id;

        return $data;
    }
}
