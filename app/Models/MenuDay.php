<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuDay extends Model
{
    use HasFactory, SoftDeletes;

    protected $days = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];

    protected $fillable = ['menu_id', 'day_of_week', 'date', 'notes'];

    protected $casts = [
        'date' => 'date',
        'menu_id' => 'integer',
        'day_of_week' => 'integer',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * @return BelongsToMany
     */
    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'menu_day_recipes')
            ->withPivot('servings')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function getDayNameAttribute()
    {
        return $this->days[$this->day_of_week] ?? 'Неизвестно';
    }
}
