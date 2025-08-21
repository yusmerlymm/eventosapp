<?php

namespace Modules\Events\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

use Modules\Events\Models\Event;

class EventController extends Controller
{
     public function index(Request $request){
        // dd('Entró al index');

        // $events = Event::orderBy("created_at","desc")->paginate(10);
        // return response()->json($events);
        $events = Event::with('venues')->get();
        return response()->json($events);
    }

    public function store(Request $request){
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
        ]);

        if ($validaciones->fails()) {
            return response()->json(['errors' => $validaciones->errors()], 422);
        }else{
            $event = new Event();
            $event->nombre = $request->nombre;
            $event->descripcion = $request->descripcion;
            $event->fecha_inicio = $request->fecha_inicio;
            $event->fecha_fin = $request->fecha_fin;
            $event->capacidad_max = $request->capacidad_max;
            $event->venues_id = $request->venues_id;
            $event->id_categoria_evento = $request->categoryId;
            $event->id_tipo_evento = $request->typeId;
            $event->status = $request->status;

            // se cambiara la logica de guardado de las imagenes
            // if ($request->hasFile('ruta_img')) {
            //     $rutaImg = $request->file('ruta_img')->store('eventos', 'public');
            //     $event->ruta_img = $rutaImg;
            // }

            $event->save();

            return response()->json(['mesasge' =>'evento creado'], 201);
        }
    }
}
