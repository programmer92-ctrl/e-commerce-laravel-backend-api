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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->decimal('total_amount', total: 10, places: 2);
            $table->decimal('subtotal_amount', total: 10, places: 2);
            $table->decimal('shipping_cost', total: 10, places: 2);
            $table->decimal('tax_amount', total: 10, places: 2);
            $table->decimal('discount_amount', total: 10 , places: 2);
            $table->string('currency');
            $table->string('status');
            $table->string('payment_method');
            $table->string('payment_status');
            $table->string('shipping_method')->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
