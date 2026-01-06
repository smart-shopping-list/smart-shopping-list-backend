<?php

namespace App\Models;

use App\Traits\HasCachedMap;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;


/**
 * Category -> категории товаров
 * @var integer $type_good_id - id типа товара
 * @var string $name - название категории товаров
 * @var string $description короткое описание категории товаров
 * @var string $icon иконка для категории
 * @var string $color цвет для сортировки
 * @var boolean $is_active флаг для определения активности
 * @var integer $sort_order поле, которое определяет порядок отображения элементов в интерфейсе
 */
class Category extends Model
{
    use HasFactory, SoftDeletes, HasCachedMap;

    /**
     * Имя таблицы, связанной с моделью.
     * @var string
     */
    protected $table = 'categories';


    protected $fillable = ['type_good_id', 'name', 'description',
        'icon', 'color', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::bootHasCachedMap();
    }

    /**
     * Отношение с продуктами
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Отношение с типами товаров
     * @return BelongsTo
     */
    public function typeGood(): BelongsTo
    {
        return $this->belongsTo(TypeGoods::class, 'type_good_id');
    }

    /**
     * Получение имени типа продукта
     * @return string|null
     */
    public function getTypeGoodNameAttribute(): ?string
    {
        return $this->typeGood?->name;
    }

    /**
     * Мутатор для color с валидацией
     * @param $value
     * @return void
     */
    public function setColorAttribute($value): void
    {
        // Проверка hex-цвета
        if ($value && !preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $value)) {
            $value = '#ccc';
        }
        $this->attributes['color'] = $value;
    }

    /**
     * Scope для получения категорий по id типа товара
     * @param Builder $query
     * @param int $typeGoodId
     * @return Builder
     */
    public function scopeOfType(Builder $query, int $typeGoodId): Builder
    {
        return $query->where('type_good_id', $typeGoodId);
    }


    /**
     * Scope для получения категорий по названию типа товара
     * @param Builder $query
     * @param string $typeName
     * @return Builder
     */
    public function scopeOfTypeName(Builder $query, string $typeName): Builder
    {
        $type = TypeGoods::findByName($typeName);

        if (!$type) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('type_good_id', $type->id);
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
     * Scope для сортировки
     * @param Builder $query
     * @return Builder
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}
