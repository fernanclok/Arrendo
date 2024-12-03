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
            $table->string('property_code', 20)->unique();
            $table->string('street', 100);
            $table->string('number', 10);
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('postal_code', 20);
            $table->decimal('rental_rate', 8, 2)->nullable();
            $table->enum('availability', ['Available', 'Not Available']);
            $table->integer('total_bathrooms');
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

            $table->string('colony', 100)->nullable();
            $table->integer('half_bathrooms')->nullable();
            $table->integer('surface_built')->nullable();
            $table->integer('total_surface')->nullable();
            $table->integer('antiquity')->nullable();
            $table->decimal('maintenance', 10, 2)->nullable();
            $table->string('state_conservation', 50)->nullable();
            $table->integer('wineries')->nullable();
            $table->integer('closets')->nullable();
            $table->integer('levels')->nullable();
            $table->integer('parking')->nullable();

            $table->json('general_features')->nullable();
            $table->json('services')->nullable();
            $table->json('exteriors')->nullable();
            $table->json('environmentals')->nullable();

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
