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
        Schema::table('venues', function (Blueprint $table) {
            $table->dropForeign('events_venues_venue_status_id_foreign');
            $table->renameColumn('venue_status_id', 'venue_general_status_id');
            
        });

        Schema::table('venues', function (Blueprint $table) {
           $table->foreign('venue_general_status_id')
                ->references('id')
                ->on('venue_general_statuses')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropForeign(['venue_general_status_id']);
            $table->renameColumn('venue_general_status_id', 'venue_status_id');
            
        });

        Schema::table('venues', function (Blueprint $table) {
           $table->foreign('venue_status_id')
                ->references('id')
                ->on('venue_general_statuses')
                ->onDelete('cascade');
        });
    }
};
