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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties');
            $table->foreignId('tenant_user_id')->constrained('users');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rental_amount', 10, 2);
            $table->enum('status', ['Active', 'Pending Renewal', 'Terminated']);
            $table->json('contract_path');
            $table->foreignId('owner_user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
