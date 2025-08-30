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
        Schema::table('products', function (Blueprint $table) {
            //
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->text('short_description')->nullable();
            $table->decimal('price', total: 8, places: 2);
            $table->decimal('compare_at_price', total: 8, places: 2)->nullable();
            $table->string('sku')->unqiue();
            $table->integer('stock_quantity');
            $table->boolean('is_featured')->default('false');
            $table->boolean('is_active')->default('true');
            $table->decimal('weight', total: 8, places: 2)->nullable();
            $table->string('dimensions')->nullable();
            $table->softDeletes()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
