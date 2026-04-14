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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('folio', 20)->unique();
            $table->foreignUuid('supplier_id')
                ->constrained('suppliers');
            $table->foreignUuid('created_by')
                ->nullable()
                ->references('id')
                ->on('users');
            $table->string('generation_type', 20)->default('manual'); // manual, automatic
            $table->string('status', 20)->default('draft'); // draft, sent, confirmed, received, cancelled
            $table->timestampTz('created_at')->default(DB::raw('now()'));
            $table->date('required_by')->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
