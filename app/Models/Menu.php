<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Menu -> меню на неделю
 * @var integer $user_id - id пользователя
 * @var string $start_date - первый день недели
 * @var string $end_date - последний день недели
 * @var string $name - название
 * @var boolean $is_active флаг для определения активности
*/
class Menu extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Имя таблицы, связанной с моделью.
     * @var string
     */
    protected $table = 'menus';

    protected $fillable = ['user_id', 'start_date', 'end_date', 'name', 'is_active'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function days(): HasMany
    {
        return $this->hasMany(MenuDay::class);
    }

    /**
     * @return HasOne
     */
    public function shoppingList(): HasOne
    {
        return $this->hasOne(ShoppingList::class);
    }

    /**
     * Активное меню пользователя
     */
    public static function getActiveForUser($userId): ?self
    {
        return static::where('user_id', $userId)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->first();
    }

    /**
     * Сделать это меню активным (деактивировать другие)
     */
    public function activate(): void
    {
        // Деактивируем другие меню пользователя
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_active' => false]);

        $this->is_active = true;
        $this->save();
    }
    /**
     * Scope для активных
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Генерация списка покупок на основе меню
     */
    public function generateShoppingList(): ShoppingList
    {
        $aggregated = [];

        foreach ($this->days as $day) {
            foreach ($day->recipes as $recipe) {
                foreach ($recipe->ingredients as $ingredient) {
                    $key = $ingredient->product_id . '|' . $ingredient->unit_id;

                    if (!isset($aggregated[$key])) {
                        $aggregated[$key] = [
                            'product_id' => $ingredient->product_id,
                            'unit_id' => $ingredient->unit_id,
                            'quantity' => 0,
                            'notes' => [],
                        ];
                    }

                    // Учитываем количество порций
                    $servings = $day->pivot->servings ?? 1;
                    $quantity = $ingredient->quantity * $servings;

                    $aggregated[$key]['quantity'] += $quantity;
                    $aggregated[$key]['notes'][] = [
                        'recipe' => $recipe->name,
                        'day' => $day->day_of_week,
                        'servings' => $servings,
                    ];
                }
            }
        }

        // Создаем или обновляем список покупок
        $shoppingList = $this->shoppingList ?? new ShoppingList();
        $shoppingList->fill([
            'user_id' => $this->user_id,
            'name' => "Список покупок: " . ($this->name ?? $this->start_date->format('d.m')),
            'menu_id' => $this->id,
            'status' => 'active',
        ]);
        $shoppingList->save();

        // Очищаем старые элементы
        $shoppingList->items()->delete();

        // Добавляем новые элементы
        foreach ($aggregated as $item) {
            $shoppingList->items()->create([
                'product_id' => $item['product_id'],
                'unit_id' => $item['unit_id'],
                'quantity' => $item['quantity'],
                'notes' => json_encode($item['notes']),
            ]);
        }

        return $shoppingList;
    }

    /**
     * Создать копию меню на следующую неделю
     */
    public function copyToNextWeek(): self
    {
        $newMenu = $this->replicate();
        $newMenu->start_date = $this->start_date->addWeek();
        $newMenu->end_date = $this->end_date->addWeek();
        $newMenu->is_active = false;
        $newMenu->push();

        // Копируем дни
        foreach ($this->days as $day) {
            $newDay = $day->replicate();
            $newDay->menu_id = $newMenu->id;
            $newDay->date = $day->date->addWeek();
            $newDay->push();

            // Копируем рецепты дней
            foreach ($day->recipes as $recipe) {
                $newDay->recipes()->attach($recipe->id, [
                    'servings' => $recipe->pivot->servings,
                ]);
            }
        }

        return $newMenu;
    }

    /**
     * Проверка, что меню соответствует неделе (start_date - понедельник, end_date - воскресенье)
     */
    public function validateWeekDates(): bool
    {
        return $this->start_date->isMonday() &&
            $this->end_date->isSunday() &&
            $this->start_date->diffInDays($this->end_date) === 6;
    }

    protected static function booted()
    {
        // При создании меню создаем 7 дней
        static::created(function ($menu) {
            $startDate = $menu->start_date;

            for ($i = 0; $i < 7; $i++) {
                $menu->days()->create([
                    'day_of_week' => $i,
                    'date' => $startDate->copy()->addDays($i),
                ]);
            }
        });

        // При удалении меню удаляем связанные данные
        static::deleting(function ($menu) {
            $menu->shoppingList()->delete();
        });
    }
}
