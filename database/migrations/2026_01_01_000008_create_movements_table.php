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
        Schema::create('movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type', 25); // receipt, dispatch, positive_adjustment, negative_adjustment, waste, return
            $table->foreignUuid('lot_id')
                ->constrained('lots');
            $table->foreignUuid('product_id')
                ->constrained('products');
            $table->decimal('quantity', 12, 3);
            $table->decimal('qty_before', 12, 3);
            $table->decimal('qty_after', 12, 3);
            $table->uuid('reference_id')->nullable();
            $table->string('reference_type', 30)->nullable();
            $table->foreignUuid('user_id')
                ->constrained('users');
            $table->text('notes')->nullable();
            $table->timestampTz('created_at')->default(DB::raw('now()'));

            // Indices - NO updated_at, this table is immutable
            $table->index(['product_id', 'lot_id', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
