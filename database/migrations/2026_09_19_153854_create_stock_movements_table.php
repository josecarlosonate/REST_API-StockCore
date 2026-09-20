<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->integer('quantity')->comment('Quantity entered for the stock movement');
            $table->integer('quantity_before')->comment('Stock quantity before the movement');
            $table->integer('quantity_after')->comment('Stock quantity after the movement');
            $table->text('reason')->nullable();
            $table->timestamps();
        });
        // Constraints
        DB::statement(
            "ALTER TABLE stock_movements 
            ADD CONSTRAINT stock_movements_quantity_before_non_negative CHECK (quantity_before >= 0),
            ADD CONSTRAINT stock_movements_quantity_after_non_negative CHECK (quantity_after >= 0),
            ADD CONSTRAINT stock_movements_type_quantity_valid
                CHECK ((type IN ('entry', 'exit') AND quantity > 0)  OR
                      (type = 'adjustment' AND quantity >= 0)
            )"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
