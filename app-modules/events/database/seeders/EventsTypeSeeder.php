<?php

namespace Modules\Events\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Events\Models\EventsType;

class EventsTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'nombre_tipo_evento' => 'Graduaciones',
                'id_categoria' => 1,
            ],
            [
                'nombre_tipo_evento' => 'Aniversarios',
                'id_categoria' => 1,
            ],
            [
                'nombre_tipo_evento' => 'Conciertos',
                'id_categoria' => 2,
            ],
            [
                'nombre_tipo_evento' => 'Festivales',
                'id_categoria' => 2,
            ],
            [
                'nombre_tipo_evento' => 'Exposiciones de arte',
                'id_categoria' => 2,
            ],
            [
                'nombre_tipo_evento' => 'Obras de teatro',
                'id_categoria' => 2,
            ],
            [
                'nombre_tipo_evento' => 'Ferias de libros',
                'id_categoria' => 2,
            ],
            [
                'nombre_tipo_evento' => 'Conferencias',
                'id_categoria' => 3,
            ],
            [
                'nombre_tipo_evento' => 'Seminarios',
                'id_categoria' => 3,
            ],
            [
                'nombre_tipo_evento' => 'Ferias comerciales',
                'id_categoria' => 3,
            ],
            [
                'nombre_tipo_evento' => 'Clases magistrales',
                'id_categoria' => 4,
            ],
            [
                'nombre_tipo_evento' => 'Jornadas',
                'id_categoria' => 4,
            ],
            [
                'nombre_tipo_evento' => 'Torneos',
                'id_categoria' => 5,
            ],
            [
                'nombre_tipo_evento' => 'Partidos',
                'id_categoria' => 5,
            ],
            [
                'nombre_tipo_evento' => 'Votaciones',
                'id_categoria' => 6,
            ],
            [
                'nombre_tipo_evento' => 'Actos publicos',
                'id_categoria' => 6,
            ],
            [
                'nombre_tipo_evento' => 'Ceremonias conmemorativas',
                'id_categoria' => 6,
            ],
            [
                'nombre_tipo_evento' => 'Conciertos beneficos',
                'id_categoria' => 7,
            ],
        ];

        foreach ($types as $type){
            EventsType::create([
                'nombre_tipo_evento' => $type['nombre_tipo_evento'],
                'id_categoria' => $type['id_categoria'],
            ]);
        }
    }
}
