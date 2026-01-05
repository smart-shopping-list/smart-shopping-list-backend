<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * TypeGoods -> тип товара
 * @var string $name - название типа товара
 * @var string $description описание типа товара
 */
class TypeGoods extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Имя таблицы, связанной с моделью.
     *
     * @var string
     */
    protected $table = 'type_goods';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * Отношение с категориями
     * @return HasMany
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'type_good_id');
    }

    /**
     * Отношение с продуктами
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'type_good_id');
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
