<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

/**
 * Recipe -> рецепты
 *
 * @var string $name - название рецепта
 * @var string $description описание рецепта
 * @var string $type тип рецепта
 * @var integer $cooking_time время приготовления
 * @var integer $servings количество порций
 * @var string $instructions пошаговое приготовление
 * @var string $source_url источник рецепта
 * @var integer $user_id - id пользователя добавившего рецепт
 * @var boolean $is_public флаг для определения опубликован ли рецепт
 */
class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Имя таблицы, связанной с моделью.
     * @var string
     */
    protected $table = 'recipes';

    protected $fillable = ['name', 'description', 'type',
        'cooking_time', 'servings', 'instructions',
        'source_url', 'user_id', 'is_public'];

    protected $casts = [
        'cooking_time' => 'integer',
        'servings' => 'integer',
        'is_public' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Значения по умолчанию для атрибутов модели.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => 'food',
        'servings' => 1,
        'is_public' => true,
        'cooking_time' => 5,
    ];

    /**
     * Получает список допустимых значений для поля type.
     *
     * @return array
     */
    public static function getTypeOptions(): array
    {
        return [
            'food' => 'Еда',
            'drink' => 'Напитки',
            'other' => 'Иное',
        ];
    }

    /**
     * Получить человеко-читаемое название типа
     * @return string
     */
    public function getTypeLabelAttribute(): string
    {
        return self::getTypeOptions()[$this->type] ?? $this->type;
    }

    /**
     * @return HasMany
     */
    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class)
            ->orderBy('sort_order');
    }

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'ingredients')
            ->using(Ingredient::class)
            ->withPivot('quantity', 'unit_id', 'notes', 'is_optional', 'sort_order')
            ->withTimestamps();
    }

    /**
     * Отношение к пользователю
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany
     */
    public function menuDays(): BelongsToMany
    {
        return $this->belongsToMany(MenuDay::class, 'menu_day_recipe')
            ->withPivot('servings')
            ->withTimestamps();
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
     * @return MorphOne
     */
    public function mainImage(): MorphOne
    {
        return $this->morphOne(Media::class, 'mediable')
            ->where('is_main', true)
            ->where('collection', 'images');
    }

    /**
     * Пошаговые фото приготовления
     * @return MorphMany
     */
    public function stepImages(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')
            ->where('collection', 'steps')
            ->ordered();
    }

    /**
     * Видео рецепта
     * @return MorphMany
     */
    public function videos(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')
            ->where('type', 'video')
            ->ordered();
    }

    /**
     * Акцессор для обратной совместимости
     * @return null|string
     */
    public function getImageAttribute(): ?string
    {
        return $this->mainImage?->url;
    }

    /**
     * @return null|string
     */
    public function getImageAltAttribute(): ?string
    {
        return $this->mainImage?->alt;
    }

    /**
     * Возвращает время приготовления в формате "11 ч 11 мин"
     * @return string|null
     */
    public function getFormattedCookingTimeAttribute(): ?string
    {
        if (!$this->cooking_time) return null;

        $hours = floor($this->cooking_time / 60);
        $minutes = $this->cooking_time % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} ч {$minutes} мин";
        } elseif ($hours > 0) {
            return "{$hours} ч";
        } else {
            return "{$minutes} мин";
        }
    }

    /**
     * Scope для поиска
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Scope для получения рецепта по типу
     * @param Builder $query
     * @param string $type
     * @return Builder
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope для активных
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_public', true);
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
