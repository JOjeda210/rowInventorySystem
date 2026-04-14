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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 20)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->foreignUuid('category_id')
                ->constrained('categories');
            $table->foreignUuid('unit_id')
                ->constrained('units_of_measure');
            $table->string('barcode', 50)->unique()->nullable();
            $table->decimal('current_stock', 12, 3)->default(0);
            $table->decimal('min_stock', 12, 3)->default(0);
            $table->decimal('max_stock', 12, 3)->nullable();
            $table->foreignUuid('location_id')
                ->nullable()
                ->references('id')
                ->on('locations');
            $table->integer('shelf_life_days')->nullable();
            $table->foreignUuid('preferred_supplier_id')
                ->nullable()
                ->references('id')
                ->on('suppliers');
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
