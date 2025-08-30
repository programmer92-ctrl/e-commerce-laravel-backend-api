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
        Schema::create('order__items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->contstrained()->onDelete('cascade');
            $table->foreignId('product_id')->contstrained()->onDelete('cascade');
            $table->foreignId('product_sku_id')->contstrained()->onDelete('cascade')->nullable();
            $table->string('product_name');
            $table->string('product_sku_code')->nullable();
            $table->decimal('product_price', total: 10, places: 2);
            $table->integer('quantity');
            $table->decimal('subtotal', total:10, places: 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order__items');
    }
};
