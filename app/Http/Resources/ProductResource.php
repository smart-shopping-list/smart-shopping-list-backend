<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'is_active' => $this->is_active,
            'alternative_names' => $this->alternative_names,

            // Связи
            'type_good_id' => $this->type_good_id,
            'type_good_name' => $this->whenLoaded('typeGood', function () {
                return $this->typeGood->name;
            }),
            'category_id' => $this->category_id,
            'category_name' => $this->whenLoaded('category', function () {
                return $this->category->name;
            }),
            'default_unit_id' => $this->default_unit_id,
            'default_unit_name' => $this->whenLoaded('defaultUnit', function () {
                return $this->defaultUnit->name;
            }),
            'default_unit_short_name' => $this->whenLoaded('defaultUnit', function () {
                return $this->defaultUnit->short_name;
            }),

            // Упаковка
            'package_amount' => $this->package_amount,
            'package_unit_id' => $this->package_unit_id,
            'package_unit_name' => $this->whenLoaded('packageUnit', function () {
                return $this->packageUnit?->name;
            }),
            'package_unit_short_name' => $this->whenLoaded('packageUnit', function () {
                return $this->packageUnit?->short_name;
            }),
            'package_size' => $this->package_size,
            'package_display' => $this->package_display,

            // Медиа
            'main_image' => $this->whenLoaded('mainImage', function () {
                return $this->mainImage?->url;
            }),
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(fn($image) => $image->url);
            }),

            // Пищевая ценность
            'nutrition' => new ProductNutritionResource($this->whenLoaded('productNutrition')),

            // Метаданные
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
