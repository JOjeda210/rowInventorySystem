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
        Schema::create('lots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('lot_number', 50);
            $table->foreignUuid('product_id')
                ->constrained('products');
            $table->foreignUuid('supplier_id')
                ->nullable()
                ->references('id')
                ->on('suppliers');
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestampTz('received_at')->default(DB::raw('now()'));
            $table->decimal('initial_qty', 12, 3);
            $table->decimal('current_qty', 12, 3);
            $table->foreignUuid('location_id')
                ->nullable()
                ->references('id')
                ->on('locations');
            $table->string('status', 20)->default('available'); // available, expiring_soon, critical, expired, depleted, blocked
            $table->string('alert_level', 15)->default('ok'); // ok, warn_7d, urgent_3d, remove_1d, expired
            $table->text('notes')->nullable();
            $table->timestampsTz();

            // Indices
            $table->unique(['product_id', 'lot_number']);
            $table->index('expiry_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
