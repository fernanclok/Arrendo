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
            $table->string('street', 100);
            $table->string('number', 10);
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('postal_code', 20);
            $table->decimal('rental_rate', 8, 2)->nullable();
            $table->enum('availability', ['Available', 'Not Available']);
            $table->decimal('total_bathrooms', 8, 2);
            $table->integer('total_rooms');
            $table->integer('total_m2');
            $table->boolean('have_parking')->nullable();
            $table->boolean('accept_mascots')->nullable();
            $table->decimal('property_price', 10, 2);
            $table->text('property_details');
            $table->json('property_photos_path');
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
