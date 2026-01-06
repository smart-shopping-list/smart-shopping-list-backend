<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'type_label' => $this->type_label,

            // Время и порции
            'cooking_time' => $this->cooking_time,
            'formatted_cooking_time' => $this->formatted_cooking_time,
            'servings' => $this->servings,

            // Инструкции и источник
            'instructions' => $this->instructions,
            'source_url' => $this->source_url,

            // Статус и автор
            'is_public' => $this->is_public,
            'user_id' => $this->user_id,
            'user_name' => $this->whenLoaded('user', function () {
                return $this->user?->name;
            }),

            // Медиа
            'main_image' => $this->whenLoaded('mainImage', function () {
                return $this->mainImage?->url;
            }),
            'step_images' => $this->whenLoaded('stepImages', function () {
                return $this->images->map(fn($image) => $image->url);
            }),

            // Ингридиенты
            'ingredients' => new IngredientResource($this->whenLoaded('ingredients')),

            // Статистика
            'ingredients_count' => $this->whenCounted('ingredients'),

            // Метаданные
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
