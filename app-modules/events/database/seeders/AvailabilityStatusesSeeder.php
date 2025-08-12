<?php

namespace Modules\Events\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Events\Models\AvailabilityStatuses;

class AvailabilityStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Disponible',
            'Reservado',
            'Mantenimiento',
            'Pendiente por confirmacion',
            'Clausurado',
        ];

        foreach($statuses as $status){
            AvailabilityStatuses::create([
                "status_name" => $status
            ]);
        }
    }
}
