<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductNutritionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'calories_per_100' => $this->calories_per_100,
            'protein_per_100' => $this->protein_per_100,
            'fat_per_100' => $this->fat_per_100,
            'carbs_per_100' => $this->carbs_per_100,
            'sugar_per_100' => $this->sugar_per_100,
            'fiber_per_100' => $this->fiber_per_100,
            'salt_per_100' => $this->salt_per_100,
            'cholesterol_per_100' => $this->cholesterol_per_100,
            'vitamin_c_per_100' => $this->vitamin_c_per_100,
            'iron_per_100' => $this->iron_per_100,
            'calcium_per_100' => $this->calcium_per_100,
        ];
    }
}
