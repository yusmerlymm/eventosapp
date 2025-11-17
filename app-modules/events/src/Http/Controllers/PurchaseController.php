<?php

namespace Modules\Events\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Events\Models\Purchase;
use Modules\Events\Models\PurchaseItem;
use Modules\Events\Models\Event;
use Modules\Events\Models\EventTicketType;

class PurchaseController
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'items' => 'required|array|min:1',
            'items.*.ticket_type_id' => 'required|exists:event_ticket_types,id',
            'items.*.cantidad' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $event = Event::findOrFail($request->event_id);
            $total = 0;
            $itemsData = [];

            // Calcular total de entradas a comprar
            $totalEntradas = 0;
            foreach ($request->items as $item) {
                $totalEntradas += $item['cantidad'];
            }
            
            // Verificar disponibilidad de capacidad
            if ($event->capacidad_max < $totalEntradas) {
                throw new \Exception('No hay suficiente capacidad disponible');
            }
            
            // Calcular total y validar disponibilidad
            foreach ($request->items as $item) {
                $ticketType = EventTicketType::findOrFail($item['ticket_type_id']);
                
                // Verificar que el ticket pertenece al evento
                if ($ticketType->event_id != $request->event_id) {
                    throw new \Exception('El tipo de entrada no pertenece a este evento');
                }

                $subtotal = $ticketType->precio * $item['cantidad'];
                $total += $subtotal;

                $itemsData[] = [
                    'ticket_type_id' => $ticketType->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $ticketType->precio,
                    'subtotal' => $subtotal,
                ];
            }

            // Crear la compra
            $purchase = Purchase::create([
                'user_id' => auth()->id(),
                'event_id' => $request->event_id,
                'codigo_compra' => Purchase::generateCode(),
                'total' => $total,
                'estado' => 'completado',
            ]);

            // Crear los items de la compra
            foreach ($itemsData as $itemData) {
                $purchase->items()->create($itemData);
            }
            
            // Descontar las entradas de la capacidad del evento
            $event->decrement('capacidad_max', $totalEntradas);

            // Si la capacidad llega a 0 o menos, marcar el evento como "Lleno"
            $event->refresh();
            if ($event->capacidad_max <= 0) {
                $fullStatus = \Modules\Events\Models\EventStatus::where('status', 'Lleno')->first();
                if ($fullStatus) {
                    $event->status = $fullStatus->id;
                    $event->save();
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Compra realizada exitosamente',
                'purchase' => $purchase->load(['items.ticketType', 'event'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating purchase:', ['message' => $e->getMessage()]);
            return response()->json([
                'error' => 'Error al procesar la compra',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $purchases = Purchase::with(['event', 'items.ticketType'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($purchases);
    }

    public function show($id)
    {
        $purchase = Purchase::with(['event', 'items.ticketType', 'user'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return response()->json($purchase);
    }

    // Para admin: ver todas las compras
    public function adminIndex()
    {
        $purchases = Purchase::with(['event', 'user', 'items.ticketType'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($purchases);
    }
}
