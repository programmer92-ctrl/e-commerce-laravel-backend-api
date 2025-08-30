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
        Schema::table('cart_items', function (Blueprint $table) {
            //
            $table->dropForeign(['product_sku_id']); // Drops the foreign key constraint
            $table->dropColumn('product_sku_id'); // Drops the column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            //
            $table->foreignId('product_sku_id')->constrained()->onDelete('cascade');
        });
    }
};
