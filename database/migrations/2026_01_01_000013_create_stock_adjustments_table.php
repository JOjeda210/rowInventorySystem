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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('folio', 20)->unique();
            $table->string('type', 30); // physical_count, waste, return, correction
            $table->text('reason');
            $table->foreignUuid('approved_by')
                ->nullable()
                ->references('id')
                ->on('users');
            $table->foreignUuid('performed_by')
                ->constrained('users');
            $table->timestampTz('performed_at')->default(DB::raw('now()'));
            $table->string('status', 20)->default('draft'); // draft, approved, rejected
            $table->text('notes')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
