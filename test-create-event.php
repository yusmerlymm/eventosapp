<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\Events\Models\Event;
use Modules\Events\Models\EventTicketType;

// Crear evento de prueba con todos los datos
$event = Event::create([
    'nombre' => 'Evento de Prueba Completo',
    'descripcion' => 'Este es un evento de prueba con todos los campos',
    'fecha_inicio' => '2025-12-01 10:00:00',
    'fecha_fin' => '2025-12-01 18:00:00',
    'capacidad_max' => 500,
    'venues_id' => 1,
    'id_categoria_evento' => 1,
    'id_tipo_evento' => 1,
    'status' => 2,
    'audiencia' => 'general',
    'venta_inicio' => '2025-11-15 08:00:00',
    'venta_fin' => '2025-11-30 23:59:00',
]);

echo "Evento creado con ID: {$event->id}\n";

// Crear tipos de entrada
$tickets = [
    ['nombre' => 'General', 'precio' => 50.00],
    ['nombre' => 'VIP', 'precio' => 150.00],
    ['nombre' => 'Estudiante', 'precio' => 25.00],
];

foreach ($tickets as $ticket) {
    EventTicketType::create([
        'event_id' => $event->id,
        'nombre' => $ticket['nombre'],
        'precio' => $ticket['precio'],
    ]);
    echo "Tipo de entrada creado: {$ticket['nombre']} - \${$ticket['precio']}\n";
}

echo "\nEvento completo creado exitosamente!\n";
echo "Visita: http://127.0.0.1:8000/events/{$event->id}\n";
