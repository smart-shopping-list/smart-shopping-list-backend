<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('type')->default('food');
            $table->text('description')->nullable();
            $table->integer('cooking_time')->nullable(); // В минутах
            $table->integer('servings')->default(1); // Количество порций
            $table->text('instructions')->nullable(); // Пошаговое приготовление
            $table->string('source_url')->nullable(); // Источник рецепта
            $table->foreignId('user_id')->nullable()->after('id')->constrained(); // Кто добавил (админ)
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'is_public', 'deleted_at']);
            $table->index(['user_id', 'deleted_at']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
