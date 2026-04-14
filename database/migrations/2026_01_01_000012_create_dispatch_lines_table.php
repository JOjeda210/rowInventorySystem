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
        Schema::create('dispatch_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('dispatch_id')
                ->constrained('dispatches')
                ->onDelete('cascade');
            $table->foreignUuid('product_id')
                ->constrained('products');
            $table->decimal('requested_qty', 12, 3);
            $table->decimal('delivered_qty', 12, 3)->default(0);
            $table->foreignUuid('lot_id')
                ->nullable()
                ->references('id')
                ->on('lots');
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_lines');
    }
};
