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
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->text('owner_note')->nullable()->after('evidence'); // Nota del owner
            $table->decimal('maintenance_cost', 10, 2)->nullable()->after('owner_note'); // Costo de mantenimiento
            $table->timestamp('date_review')->nullable()->after('maintenance_cost'); // Fecha de revisión automática
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropColumn('owner_note');
            $table->dropColumn('maintenance_cost');
            $table->dropColumn('date_review');
        });
    }
};
