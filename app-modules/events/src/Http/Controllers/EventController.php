<?php

namespace Modules\Events\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Modules\Events\Models\Event;
use Modules\Events\Models\EventsImg;
use Modules\Events\Models\EventTicketType;
use Modules\Events\Models\EventStatus;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::with(['venues', 'imgPrincipal', 'type', 'category', 'eventStatus'])->get();

        // Actualizar automáticamente a "Finalizado" si la fecha_fin ya pasó
        $finalStatus = EventStatus::where('status', 'Finalizado')->first();
        if ($finalStatus) {
            foreach ($events as $ev) {
                if ($ev->fecha_fin && $ev->fecha_fin < now() && $ev->status !== $finalStatus->id) {
                    $ev->status = $finalStatus->id;
                    $ev->save();
                }
            }
            // Recargar relaciones de estado después de posibles cambios
            $events->load('eventStatus');
        }

        return response()->json($events);
    }
    public function store(Request $request)
    {
        // Debug: log datos recibidos
        \Log::info('Store request data:', [
            'all' => $request->all(),
            'content_type' => $request->header('Content-Type'),
            'has_files' => $request->hasFile('files'),
            'ticket_types_type' => gettype($request->ticket_types),
            'ticket_types' => $request->ticket_types
        ]);
        
        $validaciones = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:10000',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'capacidad_max' => 'required|integer|min:1',
            'venues_id' => 'required|integer|min:1',
            'categoryId' => 'required|integer|min:1',
            'typeId' => 'required|integer|min:1',
            'status' => 'required|integer|min:1',
            'audiencia' => 'nullable|string|in:general,estudiantes,profesores,jubilados',
            'venta_inicio' => 'nullable|date',
            'venta_fin' => 'nullable|date|after_or_equal:venta_inicio',
            'ticket_types' => 'nullable|array',
            'ticket_types.*.nombre' => 'required_with:ticket_types|string|max:255',
            'ticket_types.*.precio' => 'required_with:ticket_types|numeric|min:0',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:jpg,jpeg,png|max:5120'
        ]);

        if ($validaciones->fails()) {
            return response()->json(['errors' => $validaciones->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Crear el evento
            $event = Event::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'capacidad_max' => $request->capacidad_max,
                'venues_id' => $request->venues_id,
                'id_categoria_evento' => $request->categoryId,
                'id_tipo_evento' => $request->typeId,
                'status' => $request->status,
                'audiencia' => $request->audiencia,
                'venta_inicio' => $request->venta_inicio,
                'venta_fin' => $request->venta_fin,
            ]);

            if (is_array($request->ticket_types)) {
                foreach ($request->ticket_types as $tt) {
                    EventTicketType::create([
                        'event_id' => $event->id,
                        'nombre' => $tt['nombre'],
                        'precio' => $tt['precio'],
                    ]);
                }
            }

            // Guardar imágenes si existen
            if ($request->hasFile('files')) {
                $nombreEventoSlug = Str::slug($event->nombre);
                foreach ($request->file('files') as $index => $file) {
                    $extension = $file->getClientOriginalExtension();
                    $nombreArchivo = $nombreEventoSlug . '-' . uniqid() . '.' . $extension;
                    $path = $file->storeAs('eventos', $nombreArchivo, 'public');

                    EventsImg::create([
                        'url_imagen' => $path,
                        'id_evento' => $event->id,
                        'es_principal' => $index === 0, // La primera imagen como principal
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Evento creado con éxito',
                'evento' => $event
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating event:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'Error al crear el evento',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }
    public function show($id){
        // Cargar evento con relaciones básicas primero
        $event = Event::with(['venues', 'type', 'category', 'imgPrincipal', 'images', 'eventStatus'])->findOrFail($id);

        // Actualizar automáticamente a "Finalizado" si la fecha_fin ya pasó
        $finalStatus = EventStatus::where('status', 'Finalizado')->first();
        if ($finalStatus && $event->fecha_fin && $event->fecha_fin < now() && $event->status !== $finalStatus->id) {
            $event->status = $finalStatus->id;
            $event->save();
            $event->load('eventStatus');
        }
        
        // Cargar ticket types manualmente desde la BD
        $ticketTypes = DB::table('event_ticket_types')->where('event_id', $id)->get();
        $event->ticket_types = $ticketTypes;
        
        return response()->json($event);
    }

    public function update(Request $request, $id)
    {
        // Debug: log datos recibidos
        \Log::info('Update request data:', ['id' => $id, 'data' => $request->all()]);
        
        $event = Event::findOrFail($id);

        $validaciones = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'descripcion' => 'sometimes|required|string|max:10000',
            'fecha_inicio' => 'sometimes|required|date',
            'fecha_fin' => 'sometimes|required|date|after_or_equal:fecha_inicio',
            'capacidad_max' => 'sometimes|required|integer|min:1',
            'venues_id' => 'sometimes|required|integer|min:1',
            'categoryId' => 'sometimes|required|integer|min:1',
            'typeId' => 'sometimes|required|integer|min:1',
            'status' => 'sometimes|required|integer|min:1',
            'audiencia' => 'nullable|string|in:general,estudiantes,profesores,jubilados',
            'venta_inicio' => 'nullable|date',
            'venta_fin' => 'nullable|date|after_or_equal:venta_inicio',
            'ticket_types' => 'nullable|array',
            'ticket_types.*.id' => 'nullable|integer',
            'ticket_types.*.nombre' => 'required_with:ticket_types|string|max:255',
            'ticket_types.*.precio' => 'required_with:ticket_types|numeric|min:0',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:jpg,jpeg,png|max:5120'
        ]);

        if ($validaciones->fails()) {
            return response()->json(['errors' => $validaciones->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $event->update([
                'nombre' => $request->input('nombre', $event->nombre),
                'descripcion' => $request->input('descripcion', $event->descripcion),
                'fecha_inicio' => $request->input('fecha_inicio', $event->fecha_inicio),
                'fecha_fin' => $request->input('fecha_fin', $event->fecha_fin),
                'capacidad_max' => $request->input('capacidad_max', $event->capacidad_max),
                'venues_id' => $request->input('venues_id', $event->venues_id),
                'id_categoria_evento' => $request->input('categoryId', $event->id_categoria_evento),
                'id_tipo_evento' => $request->input('typeId', $event->id_tipo_evento),
                'status' => $request->input('status', $event->status),
                'audiencia' => $request->input('audiencia', $event->audiencia),
                'venta_inicio' => $request->input('venta_inicio', $event->venta_inicio),
                'venta_fin' => $request->input('venta_fin', $event->venta_fin),
            ]);

            if (is_array($request->ticket_types)) {
                EventTicketType::where('event_id', $event->id)->delete();
                foreach ($request->ticket_types as $tt) {
                    EventTicketType::create([
                        'event_id' => $event->id,
                        'nombre' => $tt['nombre'],
                        'precio' => $tt['precio'],
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Evento actualizado con éxito', 'evento' => $event]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating event:', ['id' => $id, 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error al actualizar el evento', 'detalle' => $e->getMessage()], 500);
        }
    }

    public function cancel($id)
    {
        $event = Event::findOrFail($id);
        $cancelStatus = EventStatus::where('status', 'Cancelado')->first();
        if (!$cancelStatus) {
            return response()->json(['error' => 'No existe el estado Cancelado. Ejecuta el seeder de estados.'], 422);
        }
        $event->status = $cancelStatus->id;
        $event->save();
        return response()->json(['message' => 'Evento cancelado con éxito', 'evento' => $event]);
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $event = Event::findOrFail($id);
            
            // Eliminar tipos de entrada asociados
            DB::table('event_ticket_types')->where('event_id', $id)->delete();
            
            // Eliminar imágenes asociadas
            $images = DB::table('events_img')->where('id_evento', $id)->get();
            foreach ($images as $img) {
                // Eliminar archivo físico si existe
                if (isset($img->url_imagen) && $img->url_imagen && \Storage::disk('public')->exists($img->url_imagen)) {
                    \Storage::disk('public')->delete($img->url_imagen);
                }
            }
            DB::table('events_img')->where('id_evento', $id)->delete();
            
            // Eliminar el evento
            $event->delete();
            
            DB::commit();
            return response()->json(['message' => 'Evento eliminado con éxito']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting event:', ['id' => $id, 'message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al eliminar el evento', 'detalle' => $e->getMessage()], 500);
        }
    }
}


