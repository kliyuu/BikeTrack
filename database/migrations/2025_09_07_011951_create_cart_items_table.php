<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('cart_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->foreignId('product_id')->constrained()->onDelete('cascade');
      $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
      $table->string('name');
      $table->string('sku');
      $table->decimal('price', 10, 2);
      $table->unsignedInteger('quantity')->default(1);
      $table->unsignedInteger('available_stock')->default(0);
      $table->string('image')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('cart_items');
  }
};
