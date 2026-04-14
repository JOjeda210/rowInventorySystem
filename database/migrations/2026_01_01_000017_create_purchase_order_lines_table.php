<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_order_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')
                ->constrained('purchase_orders')
                ->onDelete('cascade');
            $table->foreignUuid('product_id')
                ->constrained('products');
            $table->decimal('ordered_qty', 12, 3);
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->foreignUuid('origin_alert_id')
                ->nullable()
                ->references('id')
                ->on('alerts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_lines');
    }
};
