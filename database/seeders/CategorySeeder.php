<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\TypeGoods;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $foodType = TypeGoods::where('name', 'Продукты')->first();
        $householdType = TypeGoods::where('name', 'Бытовая химия')->first();
        $hygieneType = TypeGoods::where('name', 'Гигиенические товары')->first();
        $otherType = TypeGoods::where('name', 'Прочее')->first();

        $sortOrder = 10;
        // Список категорий продуктов с описаниями
        $categories = [
            // ПРОДУКТЫ ПИТАНИЯ
            [
                'name' => 'Овощи и зелень',
                'description' => 'Свежие и замороженные овощи, а также зелень для салатов и гарниров.',
                'type_good_id' => $foodType->id,
                'color' => '#4CAF50',
            ],
            [
                'name' => 'Фрукты и ягоды',
                'description' => 'Свежие, сушеные и консервированные фрукты, а также ягоды для десертов и перекусов.',
                'type_good_id' => $foodType->id,
                'color' => '#FF9800',
            ],
            [
                'name' => 'Зерновые и крупы',
                'description' => 'Различные виды круп, макаронных изделий и хлебобулочных продуктов.',
                'type_good_id' => $foodType->id,
                'color' => '#795548',
            ],
            [
                'name' => 'Молочные продукты',
                'description' => 'Молоко, сыр, творог, йогурт и другие продукты на основе молока.',
                'type_good_id' => $foodType->id,
                'color' => '#2196F3',
            ],
            [
                'name' => 'Мясные продукты',
                'description' => 'Говядина, свинина, баранина, птица и мясные полуфабрикаты.',
                'type_good_id' => $foodType->id,
                'color' => '#F44336',
            ],
            [
                'name' => 'Рыба и морепродукты',
                'description' => 'Свежая, замороженная и консервированная рыба, а также морепродукты.',
                'type_good_id' => $foodType->id,
                'color' => '#03A9F4',
            ],
            [
                'name' => 'Хлеб и выпечка',
                'description' => 'Широкая категория пищевых продуктов, получаемых методом выпекания теста',
                'type_good_id' => $foodType->id,
                'color' => '#FFC107'
            ],
            [
                'name' => 'Макаронные изделия',
                'description' => 'Пищевой продукт из высушенного теста, замешенного на воде (иногда с добавлением других ингредиентов)',
                'type_good_id' => $foodType->id,
                'color' => '#FFC107'
            ],
            [
                'name' => 'Яйца',
                'description' => 'Куриные, утиные и гусиные яйца для приготовления различных блюд.',
                'type_good_id' => $foodType->id,
                'color' => '#FFEB3B',
            ],
            [
                'name' => 'Жиры и масла',
                'description' => 'Растительные и животные жиры, такие как масло, маргарин и сливки.',
                'type_good_id' => $foodType->id,
                'color' => '#FFE082',
            ],
            [
                'name' => 'Пряности и приправы',
                'description' => 'Соль, перец, специи, соусы и другие ингредиенты для приправ.',
                'type_good_id' => $foodType->id,
                'color' => '#8BC34A',
            ],
            [
                'name' => 'Сладости и десерты',
                'description' => 'Шоколад, конфеты, печенье, торты и другие сладкие лакомства.',
                'type_good_id' => $foodType->id,
                'color' => '#E91E63',
            ],
            [
                'name' => 'Напитки',
                'description' => 'Вода, соки, нектары, чай, кофе и другие напитки.',
                'type_good_id' => $foodType->id,
                'color' => '#00BCD4',
            ],
            [
                'name' => 'Алкогольные напитки',
                'description' => 'Алкогольные напитки, такие как вино, пиво и водка.',
                'type_good_id' => $foodType->id,
                'color' => '#7E57C2',
            ],
            [
                'name' => 'Готовые блюда и полуфабрикаты',
                'description' => 'Замороженные готовые блюда, пельмени, пицца и другие удобные продукты.',
                'type_good_id' => $foodType->id,
                'color' => '#009688',
            ],

            // БЫТОВАЯ ХИМИЯ И ХОЗТОВАРЫ
            [
                'name' => 'Бытовая химия',
                'description' => 'Химические средства для домашнего хозяйства: чистящие, дезинфицирующие, освежители воздуха, средства для ухода за поверхностями и бытовой техникой.',
                'type_good_id' => $householdType->id,
                'color' => '#FF5722'
            ],
            [
                'name' => 'Средства для уборки',
                'description' => 'Препараты и инвентарь для наведения чистоты: моющие растворы, губки, салфетки, швабры, вёдра, пылесосные насадки и т. п.',
                'type_good_id' => $householdType->id,
                'color' => '#795548'
            ],
            [
                'name' => 'Стиральные средства',
                'description' => 'Продукты для стирки белья: порошки, гели, капсулы, кондиционеры, отбеливатели, средства для деликатных тканей и цветного белья.',
                'type_good_id' => $householdType->id,
                'color' => '#2196F3'
            ],
            [
                'name' => 'Хозтовары',
                'description' => 'Предметы повседневного обихода: вёдра, тазы, совки, щётки, прищепки, вешалки, хозяйственные сумки, плёнки, пакеты и прочие принадлежности для дома и сада.',
                'type_good_id' => $householdType->id,
                'color' => '#607D8B'
            ],

            // ГИГИЕНА И КОСМЕТИКА
            [
                'name' => 'Гигиена',
                'description' => 'Средства личной гигиены: мыло, гели для душа, зубные пасты и щётки, дезодоранты, влажные салфетки, средства для бритья и интимной гигиены.',
                'type_good_id' => $hygieneType->id,
                'color' => '#00BCD4'
            ],
            [
                'name' => 'Косметика',
                'description' => 'Косметические средства для ухода и макияжа: кремы, лосьоны, тональные основы, туши, помады, лаки для ногтей, средства для ухода за волосами и кожей.',
                'type_good_id' => $hygieneType->id,
                'color' => '#E91E63'
            ],
            [
                'name' => 'Бумажная продукция',
                'description' => 'Изделия из бумаги для бытовых и гигиенических нужд: туалетная бумага, бумажные полотенца, салфетки, носовые платки, бумажные скатерти и одноразовые стаканы/тарелки.',
                'type_good_id' => $hygieneType->id,
                'color' => '#FFC107'
            ],

            // Прочее
            [
                'name' => 'Электроника',
                'description' => ' широкая категория товаров, объединяющая устройства, в которых ключевую роль играют электронные компоненты',
                'type_good_id' => $otherType->id,
                'color' => '#673AB7'
            ],
        ];

        // Добавление категорий в таблицу categories
        foreach ($categories as $key => $category) {
            Category::create([
                'type_good_id' => $category['type_good_id'],
                'name' => $category['name'],
                'description' => $category['description'],
                'color' => $category['color'],
                'sort_order' => $sortOrder * ($key + 1),
                'is_active' => true,
            ]);
        }
    }
}
