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
        Schema::create('dispatches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('folio', 20)->unique();
            $table->foreignUuid('requested_by')
                ->constrained('users');
            $table->string('destination', 60)->nullable();
            $table->timestampTz('requested_at')->default(DB::raw('now()'));
            $table->timestampTz('delivered_at')->nullable();
            $table->string('status', 20)->default('pending'); // pending, approved, fulfilled, partial, cancelled
            $table->foreignUuid('fulfilled_by')
                ->nullable()
                ->references('id')
                ->on('users');
            $table->text('notes')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatches');
    }
};
