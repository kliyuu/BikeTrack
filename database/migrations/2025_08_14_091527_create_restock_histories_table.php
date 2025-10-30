<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->integer('quantity_change');
            $table->enum('type', ['in', 'out']);
            $table->string('reason')->nullable()->comment('Reason for restock');
            $table->foreignId('performed_by')->constrained('users')->onDelete('cascade');
            $table->string('reference_type')->nullable()->comment('polymorphic reference type');
            $table->unsignedBigInteger('reference_id')->nullable(); // polymorphic reference ID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restock_histories');
    }
};
