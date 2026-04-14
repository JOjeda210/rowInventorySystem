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
        Schema::create('receipts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('folio', 20)->unique();
            $table->foreignUuid('supplier_id')
                ->constrained('suppliers');
            $table->timestampTz('arrived_at')->default(DB::raw('now()'));
            $table->timestampTz('registered_at')->nullable();
            $table->foreignUuid('received_by')
                ->constrained('users');
            $table->string('status', 25)->default('pending'); // pending, completed, with_discrepancies, rejected
            $table->text('notes')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
