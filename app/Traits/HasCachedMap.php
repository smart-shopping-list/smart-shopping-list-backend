<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

trait HasCachedMap
{
    /**
     * Получить закешированную карту модели (name => объект)
     */
    public static function getCachedMap(): \Illuminate\Support\Collection
    {
        $cacheKey = static::getCacheKey();

        return Cache::remember($cacheKey, 3600, function () {
            return static::getCacheQuery()
                ->get()
                ->keyBy('name');
        });
    }

    /**
     * Получить ключ кеша
     */
    public static function getCacheKey(): string
    {
        return strtolower(class_basename(static::class)) . ':map';
    }

    /**
     * Запрос для кеширования (по умолчанию активные, не удаленные)
     */
    protected static function getCacheQuery(): Builder
    {
        $query = static::query()->whereNull('deleted_at');

        // Если есть поле is_active, добавляем условие
        if (in_array('is_active', (new static)->getFillable())) {
            $query->where('is_active', true);
        }

        return $query->select('id', 'name');
    }

    /**
     * Инвалидировать кеш
     */
    public static function invalidateCache(): void
    {
        Cache::forget(static::getCacheKey());
    }

    /**
     * Найти по имени с использованием кеша
     */
    public static function findByName(string $name): ?self
    {
        $map = static::getCachedMap();
        return $map[$name] ?? null;
    }

    /**
     * Boot трейта
     */
    protected static function bootHasCachedMap(): void
    {
        static::saved(function () {
            static::invalidateCache();
        });

        static::deleted(function () {
            static::invalidateCache();
        });

        static::restored(function () {
            static::invalidateCache();
        });
    }
}
