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
        Schema::create('adjustment_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('adjustment_id')
                ->constrained('stock_adjustments')
                ->onDelete('cascade');
            $table->foreignUuid('product_id')
                ->constrained('products');
            $table->foreignUuid('lot_id')
                ->nullable()
                ->references('id')
                ->on('lots');
            $table->decimal('system_qty', 12, 3)->nullable();
            $table->decimal('physical_qty', 12, 3)->nullable();
            $table->decimal('variance', 12, 3)->nullable();
            $table->decimal('error_pct', 5, 2)->nullable();
            $table->text('probable_cause')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adjustment_lines');
    }
};
