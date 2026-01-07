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
        Schema::create('menu_day_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_day_id')->constrained()->onDelete('cascade');
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->integer('servings')->default(1); // Количество порций для этого дня
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['menu_day_id', 'recipe_id']);
            $table->index(['menu_day_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_day_recipes');
    }
};
