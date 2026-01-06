<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductNutrition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'calories_per_100',
        'protein_per_100',
        'fat_per_100',
        'carbs_per_100',
        'sugar_per_100',
        'fiber_per_100',
        'salt_per_100',
        'cholesterol_per_100',
        'vitamin_c_per_100',
        'iron_per_100',
        'calcium_per_100',
    ];

    protected $casts = [
        'calories_per_100' => 'integer',
        'protein_per_100' => 'float',
        'fat_per_100' => 'float',
        'carbs_per_100' => 'float',
        'sugar_per_100' => 'float',
        'fiber_per_100' => 'float',
        'salt_per_100' => 'float',
        'cholesterol_per_100' => 'float',
        'vitamin_c_per_100' => 'float',
        'iron_per_100' => 'float',
        'calcium_per_100' => 'float',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return null[]|string[]
     */
    public function getFormattedNutritionAttribute(): array
    {
        return [
            'calories' => $this->calories_per_100 ? $this->calories_per_100 . ' ккал' : null,
            'protein' => $this->protein_per_100 ? $this->protein_per_100 . ' г' : null,
            'fat' => $this->fat_per_100 ? $this->fat_per_100 . ' г' : null,
            'carbs' => $this->carbs_per_100 ? $this->carbs_per_100 . ' г' : null,
        ];
    }
}
