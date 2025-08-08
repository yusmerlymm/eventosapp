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
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('venues_id')
                ->nullable()
                ->constrained('venues')
                ->onDelete('cascade'); // Si se elimina un venue(recinto), los eventos asociados también se eliminarán
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('venues_id'); // Elimina tanto la columna como su clave foránea
        });
    }
};
