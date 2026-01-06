<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

/**
 * Product -> товары
 * @var integer $category_id - id категории товара
 * @var integer $type_good_id - id типа товара
 * @var integer $default_unit_id - id единицы измерения установленной по умолчанию
 *
 * @var string $name - название товара
 * @var array $alternative_names - альтернативные названия товара
 * @var string $description описание товара
 * @var boolean $is_active флаг для определения активности
 *
 * @var float $package_amount количество товара
 * @var integer $package_unit_id id единицы измерения
 * @var string $package_size произвольное отображение размера (на случай особых форматов)
 *
 *   Дефолтные значения при отсутствии unit:
 *   - name: 'шт.'
 *   - short_name: 'шт.'
 *   - conversion_rate: 1
 */
class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Имя таблицы, связанной с моделью.
     * @var string
     */
    protected $table = 'products';

    protected $fillable = [
        'name',
        'category_id',
        'type_good_id',
        'default_unit_id',

        'package_amount',
        'package_unit_id',
        'package_size',

        'description',
        'alternative_names',
        'is_active',
    ];

    protected $casts = [
        'package_amount' => 'float',
        'alternative_names' => 'array',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Отношение с категорией
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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
     * Отношение с информацией о пищевой ценности продукта
     * @return HasOne
     */
    public function productNutrition(): HasOne
    {
        return $this->hasOne(ProductNutrition::class);
    }

    /**
     * Отношение с ингредиентами рецепта
     * @return HasMany
     */
    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }

    /**
     * Отношение с рецептами через ингредиенты
     * @return BelongsToMany
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'ingredients')
            ->withPivot('quantity', 'unit', 'notes')
            ->withTimestamps();
    }

    /**
     * Единица измерения по умолчанию
     * @return BelongsTo
     */
    public function defaultUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'default_unit_id')->withDefault([
            'name' => 'шт.',
            'short_name' => 'шт.',
            'conversion_rate' => 1,
        ]);
    }

    /**
     * Единица измерения упаковки
     * @return BelongsTo
     */
    public function packageUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'package_unit_id')->withDefault([
            'name' => 'шт.',
            'short_name' => 'шт.',
            'conversion_rate' => 1,
        ]);
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
     * Получение имени категории продукта
     * @return string|null
     */
    public function getCategoryNameAttribute(): ?string
    {
        return $this->category?->name;
    }

    /**
     * Полиморфное отношение к медиа
     * @return MorphMany
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->ordered();
    }

    /**
     * Главное изображение продукта
     * @return MorphOne
     */
    public function mainImage(): MorphOne
    {
        return $this->morphOne(Media::class, 'mediable')
            ->where('is_main', true)
            ->where('collection', 'images');
    }

    /**
     * Все изображения галереи
     * @return MorphMany
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')
            ->where('collection', 'images')
            ->ordered();
    }

    /**
     * Документы (сертификаты, инструкции)
     * @return MorphMany
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')
            ->where('collection', 'documents')
            ->ordered();
    }

    /**
     * Акцессор для обратной совместимости
     * @return null
     */
    public function getImageAttribute()
    {
        return $this->mainImage?->url;
    }

    /**
     * @return null
     */
    public function getImageAltAttribute()
    {
        return $this->mainImage?->alt;
    }

    /**
     * Метод для форматированного отображения упаковки
     * @return string|null
     */
    public function getPackageDisplayAttribute(): ?string
    {
        if ($this->package_size) {
            return $this->package_size;
        }

        if ($this->package_amount && $this->packageUnit) {
            return $this->package_amount . ' ' . $this->packageUnit->short_name;
        }

        if ($this->package_amount) {
            return (string) $this->package_amount;
        }

        return null;
    }

    /**
     * Scope для получения товара по id типа товара
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
     * Scope для получения товара по id категории товара
     * @param Builder $query
     * @param int $categoryId
     * @return Builder
     */
    public function scopeOfCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope для получения категорий по названию категории товара
     * @param Builder $query
     * @param string $categoryName
     * @return Builder
     */
    public function scopeOfCategoryName(Builder $query, string $categoryName): Builder
    {
        $category = Category::findByName($categoryName);

        if (!$category) {
            return $query->whereRaw('1 = 0');  // Пустой результат
        }

        return $query->where('category_id', $category->id);
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
        return $query->orderBy('name');
    }
}
