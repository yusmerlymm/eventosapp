<?php

namespace Modules\Events\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Events\Models\EventStatus;

class EventStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            'Borrador', 
            'Publicado', 
            'Cancelado', 
            'Finalizado', 
            'En revisión',
            'Pospuesto',
            'Ventas cerradas',
            'Lleno',
            'Inactivo'
        ];

        foreach($estados as $estado){
            EventStatus::create([
                'status' => $estado
            ]);
        }
    }
}
