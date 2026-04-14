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
        Schema::create('locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->string('type', 50)->nullable();
            $table->decimal('min_temp', 5, 1)->nullable();
            $table->decimal('max_temp', 5, 1)->nullable();
            $table->decimal('capacity', 10, 3)->nullable();
            $table->foreignUuid('unit_id')
                ->nullable()
                ->references('id')
                ->on('units_of_measure');
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
