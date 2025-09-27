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
        Schema::create('attribute_option_product_sku', function (Blueprint $table) {
            
            // Foreign Key 1
            $table->unsignedBigInteger('attribute_option_id');
            $table->foreign('attribute_option_id')
                  ->references('id')
                  ->on('attribute_options')
                  ->onDelete('cascade');

            // Foreign Key 2
            $table->unsignedBigInteger('product_sku_id');
            $table->foreign('product_sku_id')
                  ->references('id')
                  ->on('product_skus')
                  ->onDelete('cascade');
            
            // Custom Column (Optional, but common for pivot tables)
            // e.g., to store the price modifier for this specific SKU/Option combination
            $table->decimal('price_modifier', 8, 2)->default(0);

            // 🔑 Define the Composite Primary Key
            $table->primary(['attribute_option_id', 'product_sku_id'], 'option_sku_primary');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_option_product_sku');
    }
};
