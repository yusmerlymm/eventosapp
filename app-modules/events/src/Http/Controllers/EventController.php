<?php

namespace Modules\Events\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Modules\Events\Models\Event;
use Modules\Events\Models\EventsImg;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::with(['venues', 'imgPrincipal', 'type', 'category'])->get();
        return response()->json($events);
    }
    public function store(Request $request)
    {
        $validaciones = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'capacidad_max' => 'required|integer|min:1',
            'venues_id' => 'required|integer|min:1',
            'categoryId' => 'required|integer|min:1',
            'typeId' => 'required|integer|min:1',
            'status' => 'required|integer|min:1',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:jpg,jpeg,png|max:5120' // Ajusta según tus necesidades
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
            ]);

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
            return response()->json([
                'error' => 'Error al crear el evento',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }
    public function show($id){
        $events = Event::with(['venues', 'type', 'category', 'imgPrincipal'])->findOrFail($id); 
        return response()->json($events);
    }
}


