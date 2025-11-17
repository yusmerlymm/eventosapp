<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('events_category')) {
            Schema::create('events_category', function (Blueprint $table) {
                $table->id();
                $table->string('nombre_categoria');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('events_category');
    }
};
