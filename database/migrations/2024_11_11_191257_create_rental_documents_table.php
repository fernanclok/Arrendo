<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('rental_documents_application', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('rental_applications');
            $table->json('document_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_documents');
    }
};
