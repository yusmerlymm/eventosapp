<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Events\Models\Venue;
use Modules\Events\Models\VenueStatuses;

class VenueExampleSeeder extends Seeder
{
    public function run(): void
    {
        $status = VenueStatuses::first();
        if (! $status) {
            $status = VenueStatuses::create(['status' => 'Disponible']);
        }

        if (! Venue::query()->exists()) {
            Venue::create([
                'venue_general_status_id' => $status->id,
                'nombre' => 'Auditorio Principal',
                'direccion' => 'Av. Central 123',
                'capacidad_max' => 500,
            ]);
        }
    }
}
