<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();

            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('type_good_id')->after('category_id')->constrained('type_goods')->onDelete('restrict');

            // Единица измерения по умолчанию (для добавления в рецепты)
            $table->foreignId('default_unit_id')->constrained('units')->onDelete('restrict');

            // Данные об упаковке (для отображения в каталоге)
            $table->decimal('package_amount', 10, 2)->nullable();
            $table->foreignId('package_unit_id')->nullable()->constrained('units')->onDelete('restrict');

            // Произвольное отображение размера (на случай особых форматов)
            $table->string('package_size')->nullable(); // "450 мл", "1 л", "10 шт"

            $table->text('description')->nullable();
            $table->json('alternative_names')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type_good_id', 'category_id']);
            $table->index(['category_id', 'is_active', 'deleted_at']);
            $table->index(['default_unit_id']);
        });

        Schema::create('product_nutrition', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('calories_per_100')->nullable();
            $table->decimal('protein_per_100', 5, 2)->nullable(); // Белки
            $table->decimal('fat_per_100', 5, 2)->nullable(); // Жиры
            $table->decimal('carbs_per_100', 5, 2)->nullable(); // Углеводы
            $table->decimal('sugar_per_100', 5, 2)->nullable(); //сахар
            $table->decimal('fiber_per_100', 5, 2)->nullable(); //клетчатка
            $table->decimal('salt_per_100', 5, 2)->nullable(); // соль (г)
            $table->decimal('cholesterol_per_100', 5, 2)->nullable(); // холестерин (мг)
            $table->decimal('vitamin_c_per_100', 5, 2)->nullable(); // витамин C (мг)
            $table->decimal('iron_per_100', 5, 2)->nullable(); // железо (мг)
            $table->decimal('calcium_per_100', 5, 2)->nullable(); // кальций (мг)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
