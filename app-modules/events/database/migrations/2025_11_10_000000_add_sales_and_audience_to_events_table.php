<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (!Schema::hasColumn('events', 'audiencia')) {
                    $table->string('audiencia')->default('general');
                }
                if (!Schema::hasColumn('events', 'venta_inicio')) {
                    $table->dateTime('venta_inicio')->nullable();
                }
                if (!Schema::hasColumn('events', 'venta_fin')) {
                    $table->dateTime('venta_fin')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (Schema::hasColumn('events', 'audiencia')) {
                    $table->dropColumn('audiencia');
                }
                if (Schema::hasColumn('events', 'venta_inicio')) {
                    $table->dropColumn('venta_inicio');
                }
                if (Schema::hasColumn('events', 'venta_fin')) {
                    $table->dropColumn('venta_fin');
                }
            });
        }
    }
};
