<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'грамм', 'short_name' => 'г', 'type' => 'weight', 'conversion_rate' => 1, 'is_default' => true, 'sort_order' => 10],
            ['name' => 'килограмм', 'short_name' => 'кг', 'type' => 'weight', 'conversion_rate' => 1000, 'is_default' => false, 'sort_order' => 20],
            ['name' => 'миллиграмм', 'short_name' => 'мг', 'type' => 'weight', 'conversion_rate' => 0.001, 'is_default' => false, 'sort_order' => 5],

            ['name' => 'миллилитр', 'short_name' => 'мл', 'type' => 'volume', 'conversion_rate' => 1, 'is_default' => true, 'sort_order' => 110],
            ['name' => 'литр', 'short_name' => 'л', 'type' => 'volume', 'conversion_rate' => 1000, 'is_default' => false, 'sort_order' => 120],

            ['name' => 'штука', 'short_name' => 'шт', 'type' => 'count', 'conversion_rate' => 1, 'is_default' => true, 'sort_order' => 210],
            ['name' => 'упаковка', 'short_name' => 'уп', 'type' => 'count', 'conversion_rate' => 1, 'is_default' => false, 'sort_order' => 220],
            ['name' => 'Порция', 'short_name' => 'порц', 'type' => 'count', 'conversion_rate' => 1, 'is_default' => false, 'sort_order' => 240],
            ['name' => 'десяток', 'short_name' => 'дес', 'type' => 'count', 'conversion_rate' => 10, 'is_default' => false, 'sort_order' => 230],

            ['name' => 'сантиметр', 'short_name' => 'см', 'type' => 'length', 'conversion_rate' => 1, 'is_default' => true, 'sort_order' => 310],
            ['name' => 'метр', 'short_name' => 'м', 'type' => 'length', 'conversion_rate' => 100, 'is_default' => false, 'sort_order' => 320],

        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
