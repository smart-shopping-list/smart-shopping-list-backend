<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date->format('Y-m-d'),
            'week_number' => $this->start_date->weekOfYear,

            // Статус и автор
            'is_active' => $this->is_active,
            'user_id' => $this->user_id,
            'user_name' => $this->whenLoaded('user', function () {
                return $this->user?->name;
            }),

            // Дни меню с рецептами
            'days' => MenuDayResource::collection($this->whenLoaded('days')),

            // Связанный список покупок
            'shopping_list' => new ShoppingListResource($this->whenLoaded('shoppingList')),

            // Статистика
            'recipes_count' => $this->whenCounted('recipes', function () {
                return $this->recipes()->count();
            }),
            'days_count' => $this->whenLoaded('days', function () {
                return $this->days->count();
            }),

            // Форматированные данные для UI
            'formatted_period' => $this->start_date->format('d.m') . ' - ' . $this->end_date->format('d.m'),
            'is_current_week' => $this->start_date->isCurrentWeek(),

            // Метаданные
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
