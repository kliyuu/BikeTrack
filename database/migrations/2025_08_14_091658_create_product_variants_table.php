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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('variant_type')->default('standard'); // 'size_color', 'model', 'specification', etc.
            $table->string('variant_name'); // Display name for the variant
            $table->json('variant_attributes')->nullable(); // Flexible JSON field for any attributes
            $table->string('size')->nullable(); // e.g., 'S', 'M', 'L', 'XL', '26"', '700c', etc.
            $table->string('color')->nullable(); // e.g., 'Red', 'Blue', 'Black'
            $table->string('model')->nullable(); // e.g., 'Honda Beat', 'Honda Wave', 'Honda Mio'
            $table->text('specifications')->nullable(); // Additional specifications
            $table->string('variant_sku')->unique(); // Unique SKU for this variant
            $table->decimal('price_adjustment', 8, 2)->default(0); // Price difference from base product
            $table->integer('stock_quantity')->default(0); // Stock for this specific variant
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
