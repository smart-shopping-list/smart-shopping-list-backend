<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Unit -> единицы измерения
 * @var string $name - название единицы измерения
 * @var string $short_name короткое название единицы измерения
 * @var string $type определяет к какому типу относится единица измерения (вес, объем, количество, длина)
 * @var float $conversion_rate значение для конвертации единица измерения
 * @var boolean $is_default флаг для определения базовой единицы измерения
 * @var integer $sort_order поле, которое определяет порядок отображения элементов в интерфейсе
*/
class Unit extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Имя таблицы, связанной с моделью.
     *
     * @var string
     */
    protected $table = 'units';

    /**
     * Указывает, должны ли модели иметь временные метки created_at и updated_at.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'short_name',
        'type',
        'conversion_rate',
        'is_default',
        'sort_order',
    ];

    /**
     * Атрибуты, которые должны быть приведены к определённому типу.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'conversion_rate' => 'decimal:4',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Значения по умолчанию для атрибутов модели.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => 'weight',
        'conversion_rate' => 1.0000,
        'is_default' => false,
        'sort_order' => 0,
    ];

    // Событие модели
    protected static function booted()
    {
        static::saving(function ($unit) {
            // Если устанавливается как единица по умолчанию,
            // снимаем флаг с других единиц этого типа
            if ($unit->is_default) {
                static::where('type', $unit->type)
                    ->where('id', '!=', $unit->id)
                    ->update(['is_default' => false]);
            }
        });
    }

    /**
     * Получает список допустимых значений для поля type.
     *
     * @return array
     */
    public static function getTypeOptions(): array
    {
        return [
            'weight' => 'Вес',
            'volume' => 'Объём',
            'count' => 'Количество',
            'length' => 'Длина',
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
     * Проверяет, является ли единица измерения основной.
     *
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Получить единицу по умолчанию для типа
     * @param string $type
     * @return self|null
     */
    public static function getDefaultUnit(string $type): ?self
    {
        return static::where('type', $type)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Конвертировать значение из этой единицы в базовую
     * @param float $value
     * @return float
     */
    public function convertToBase(float $value): float
    {
        return $value * $this->conversion_rate;
    }

    /**
     * Конвертировать значение из базовой единицы в эту
     * @param float $value
     * @return float
     */
    public function convertFromBase(float $value): float
    {
        return $this->conversion_rate != 0
            ? $value / $this->conversion_rate
            : 0;
    }

    /**
     * Scope для получения единиц по типу
     * @param Builder $query
     * @param string $type
     * @return Builder
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope для получения основных единиц измерения.
     * @param Builder $query
     * @return Builder
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope для сортировки по полю sort_order.
     * @param Builder $query
     * @return Builder
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Scope для активных (не удаленных)
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('deleted_at');
    }
}
