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
        Schema::create('events_venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_status_id')->constrained('venue_general_statuses')->onDelete('cascade');
            $table->string('nombre', 100);
            $table->string('direccion', 255)->nullable();
            $table->integer('capacidad_max');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events_venues');
    }
};
