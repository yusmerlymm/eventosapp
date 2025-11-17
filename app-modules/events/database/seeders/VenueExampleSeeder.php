<?php

namespace Modules\Events\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Events\Models\Venue;
use Modules\Events\Models\VenueStatuses;

class VenueExampleSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure there is at least one general status
        $status = VenueStatuses::first();
        if (! $status) {
            $status = VenueStatuses::create(['status' => 'Disponible']);
        }

        // Create example venue if none exists
        if (! Venue::query()->exists()) {
            Venue::create([
                'venue_general_status_id' => $status->id,
                'nombre' => 'Aula magna',
                'direccion' => 'Av. Central 123',
                'capacidad_max' => 500,
            ]);
        }
    }
}
