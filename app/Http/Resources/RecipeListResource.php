<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeListResource extends JsonResource
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
            'type_label' => $this->type_label,
            'cooking_time' => $this->cooking_time,
            'servings' => $this->servings,
            'main_image' => $this->mainImage?->url,
            'ingredients_count' => $this->ingredients_count,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
