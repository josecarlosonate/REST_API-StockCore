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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->integer('min_stock')->nullable();
            $table->integer('max_stock')->nullable();
            $table->timestamps();
        });
        // Constraints
        DB::statement(
            'ALTER TABLE inventories 
            ADD CONSTRAINT inventories_quantity_non_negative  CHECK (quantity >= 0),
            ADD CONSTRAINT inventories_min_stock_non_negative CHECK (min_stock >= 0),
            ADD CONSTRAINT max_stock_must_be_greater_than_zero CHECK (max_stock >= 0),
            ADD CONSTRAINT inventories_stock_range_valid CHECK (max_stock >= min_stock)'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
