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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('address', 100);
            $table->decimal('rental_rate', 8, 2);
            $table->enum('availability', ['Available', 'Not Available']);
            $table->integer('total_bathrooms');
            $table->integer('total_rooms');
            $table->integer('total_m2');
            $table->boolean('have_parking');
            $table->decimal('property_price', 10, 2);
            $table->foreignId('owner_user_id')->constrained('users');
            $table->foreignId('zone_id')->constrained('zones');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
