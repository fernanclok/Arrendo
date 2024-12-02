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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone', 15)->unique();
            $table->string('role', 20);
            $table->string('emergency_contact_name', 50);
            $table->string('emergency_contact_phone', 15);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->date('registration_date');
            $table->json('document_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
