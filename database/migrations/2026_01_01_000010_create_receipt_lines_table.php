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
        Schema::create('receipt_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('receipt_id')
                ->constrained('receipts')
                ->onDelete('cascade');
            $table->foreignUuid('product_id')
                ->constrained('products');
            $table->foreignUuid('lot_id')
                ->nullable()
                ->references('id')
                ->on('lots');
            $table->decimal('expected_qty', 12, 3)->nullable();
            $table->decimal('received_qty', 12, 3);
            $table->decimal('discrepancy', 12, 3)->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('lot_number', 50)->nullable();
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->foreignUuid('location_id')
                ->nullable()
                ->references('id')
                ->on('locations');
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_lines');
    }
};
