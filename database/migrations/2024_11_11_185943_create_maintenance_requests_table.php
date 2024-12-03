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
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties');
            $table->foreignId('tenant_user_id')->constrained('users');
            $table->text('description');
            $table->date('report_date');
            $table->enum('priority', ['Low', 'Medium', 'High']);
            $table->enum('status', ['Pending', 'In Progress', 'Completed']);
            $table->string('evidence');
            $table->text('owner_note')->nullable(); 
            $table->decimal('maintenance_cost', 10, 2)->nullable(); 
            $table->timestamp('date_review')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};
