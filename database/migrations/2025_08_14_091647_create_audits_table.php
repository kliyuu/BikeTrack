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
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type'); // Type of the auditable model
            $table->unsignedBigInteger('auditable_id'); // ID of the auditable model
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // User who performed the action
            $table->string('action');
            $table->json('changes')->nullable();
            $table->string('ip_address')->nullable(); // IP address of the user
            $table->string('user_agent')->nullable(); // User agent of the user
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
