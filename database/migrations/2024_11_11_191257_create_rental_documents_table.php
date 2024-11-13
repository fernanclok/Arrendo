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
        Schema::create('rental_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('rental_applications');
            $table->string('document_type');
            $table->string('document_path');
            $table->date('upload_date');
            $table->enum('document_status', ['Pending', 'Approved', 'Rejected']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_documents');
    }
};
