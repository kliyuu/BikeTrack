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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique()->comment('Unique code for the client');
            $table->string('company_name')->nullable()->comment('Company name of the client');
            $table->string('contact_name')->comment('Client name or representative');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->string('tax_number')->nullable();
            $table->text('billing_address');
            $table->text('shipping_address');

            $table->string('status')->default('pending')->comment('pending, approved, rejected');

            $table->string('payment_terms')->nullable()->comment('Net 30, Net 60, COD, etc.');
            $table->json('payment_method')->nullable()->comment('bank_transfer, credit_card, gcash, paymaya');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
