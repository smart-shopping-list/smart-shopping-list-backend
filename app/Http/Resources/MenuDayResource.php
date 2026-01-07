<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuDayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'day_of_week' => $this->day_of_week,
            'day_name' => $this->day_name,
            'date' => $this->date->format('Y-m-d'),
            'notes' => $this->notes,

            // Рецепты дня
            'recipes' => $this->whenLoaded('recipes', function () {
                return $this->recipes->map(function ($recipe) {
                    return [
                        'id' => $recipe->id,
                        'name' => $recipe->name,
                        'servings' => $recipe->pivot->servings,
                        'sort_order' => $recipe->pivot->sort_order,
                        'cooking_time' => $recipe->cooking_time,
                        'type' => $recipe->type,
                    ];
                });
            }),

            // Статистика
            'recipes_count' => $this->whenCounted('recipes'),
        ];
    }
}
