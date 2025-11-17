<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nombre');
                $table->text('descripcion');
                $table->dateTime('fecha_inicio');
                $table->dateTime('fecha_fin');
                $table->string('ubicacion')->nullable();
                $table->integer('capacidad_max');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // No drop to avoid conflicting with subsequent alters
    }
};
