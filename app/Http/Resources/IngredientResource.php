<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IngredientResource extends JsonResource
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
            'recipe_id' => $this->recipe_id,
            'product_id' => $this->product_id,
            'product_name' => $this->whenLoaded('product', function () {
                return $this->product->name;
            }),
            'product' => new ProductResource($this->whenLoaded('product')),

            'unit_id' => $this->unit_id,
            'unit_name' => $this->whenLoaded('unit', function () {
                return $this->unit->name;
            }),
            'unit_short_name' => $this->whenLoaded('unit', function () {
                return $this->unit->short_name;
            }),

            'unit' => $this->when($this->unit->exists, function () {
                return new UnitResource($this->unit);
            }, function () {
                return [
                    'name' => 'шт.',
                    'short_name' => 'шт.',
                    'is_default' => false,
                ];
            }),

            'quantity' => (float) $this->quantity,
            'formatted_quantity' => $this->formatted_quantity,
            'base_quantity' => $this->base_quantity,
            'notes' => $this->notes,
            'is_optional' => $this->is_optional,
            'sort_order' => $this->sort_order,
            'full_description' => $this->full_description, // Добавить акцессор в модель

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
