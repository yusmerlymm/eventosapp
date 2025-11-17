<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events') && ! Schema::hasColumn('events', 'ruta_img')) {
            Schema::table('events', function (Blueprint $table) {
                $table->string('ruta_img')->default('');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('events') && Schema::hasColumn('events', 'ruta_img')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('ruta_img');
            });
        }
    }
};
