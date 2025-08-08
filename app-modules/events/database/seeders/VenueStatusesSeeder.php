<?php

namespace Modules\Events\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Events\Models\VenueStatuses;
class VenueStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            "Activo",
            "Inactivo",
            "Archivado",
            "En revisión",
            "Borrador"
        ];

        foreach ($statuses as $status) {
            VenueStatuses::create([
                "status_name" => $status
            ]);
        }
    }
}
