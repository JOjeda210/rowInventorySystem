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
        Schema::create('alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type', 30); // expiry_7d, expiry_3d, expiry_1d, expired, low_stock, out_of_stock, inventory_discrepancy
            $table->foreignUuid('product_id')
                ->constrained('products');
            $table->foreignUuid('lot_id')
                ->nullable()
                ->references('id')
                ->on('lots');
            $table->text('message');
            $table->string('level', 10)->default('info'); // info, warning, critical
            $table->boolean('is_read')->default(false);
            $table->foreignUuid('read_by')
                ->nullable()
                ->references('id')
                ->on('users');
            $table->timestampTz('read_at')->nullable();
            $table->timestampsTz();

            // Indices
            $table->index('is_read');
            $table->index('type');
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
