<?php

namespace Database\Seeders;

use App\Models\TypeGoods;
use Illuminate\Database\Seeder;

class TypeGoodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Продукты',
                'description' => 'Товары, предназначенные для потребления в пищу.',
            ],
            [
                'name' => 'Бытовая химия',
                'description' => 'Группа потребительских товаров, представляющая собой непродовольственные химические вещества.',
            ],
            [
                'name' => 'Гигиенические товары',
                'description' => 'Группа потребительских товаров, предназначенные для поддержания личной гигиены, чистоты и здоровья человека.',
            ],
            [
                'name' => 'Прочее',
                'description' => 'Остальные товары, не вошедшие в основные категории.',
            ],
        ];


        foreach ($types as $type) {
            TypeGoods::create($type);
        }
    }
}
