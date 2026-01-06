<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Ingredient -> ингредиенты
 * @var integer $recipe_id id рецепта
 * @var integer $product_id id продукта
 * @var integer $unit_id id единицы измерения
 * @var integer $quantity количество
 * @var string $notes примечания
 * @var boolean $is_optional флаг для определения опциональный или нет ингридиент
 * @var integer $sort_order поле, которое определяет порядок отображения элементов в интерфейсе
 *
 *  Дефолтные значения при отсутствии unit:
 *  - name: 'шт.'
 *  - short_name: 'шт.'
 *  - conversion_rate: 1
 */
class Ingredient extends Model
{
    use HasFactory, SoftDeletes;


    /**
     * Имя таблицы, связанной с моделью.
     * @var string
     */
    protected $table = 'ingredients';

    protected $fillable = [
        'recipe_id',
        'product_id',
        'unit_id',
        'quantity',
        'notes',
        'is_optional',
        'sort_order'
    ];

    protected $casts = [
        'quantity' => 'float',
        'is_optional' => 'boolean',
        'sort_order' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class)->withDefault([
            'name' => 'Рецепт удален',
            'is_public' => false,
        ]);
    }

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withDefault([
            'name' => 'Продукт удален',
            'is_active' => false,
        ]);
    }

    /**
     * @return BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class)->withDefault([
            'name' => 'шт.',
            'short_name' => 'шт.',
            'conversion_rate' => 1,
        ]);
    }

    /**
     * Форматированное количество
     * @return string
     */
    public function getFormattedQuantityAttribute(): string
    {
        return $this->quantity . ' ' . $this->unit->short_name;
    }

    /**
     * Возвращает количество в соответствии с базовой единицей измерения
     * @return mixed
     */
    public function getBaseQuantityAttribute(): mixed
    {
        return (float) $this->quantity * $this->unit->conversion_rate;
    }

    /**
     * Создает краткое описание
     * @return string
     */
    public function getFullDescriptionAttribute(): string
    {
        $description = "{$this->formatted_quantity} {$this->product->name}";

        if ($this->notes) {
            $description .= " ({$this->notes})";
        }

        if ($this->is_optional) {
            $description .= " [опционально]";
        }

        return $description;
    }

    /**
     * Скоуп для обязательных ингредиентов
     * @param Builder $query
     * @return Builder
     */
    public function scopeRequired(Builder $query): Builder
    {
        return $query->where('is_optional', false);
    }

    /**
     * Скоуп для опциональных ингридиентов
     * @param Builder $query
     * @return Builder
     */
    public function scopeOptional(Builder $query): Builder
    {
        return $query->where('is_optional', true);
    }

    /**
     * Скоуп для сортировки
     * @param Builder $query
     * @return Builder
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}
