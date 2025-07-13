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
        return response()->json(Event::all());
    }

    public function store(Request $request){
        $validaciones = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'ubicacion' => 'required|string|max:255',
            'capacidad_max' => 'required|integer|min:1',
            'ruta_img' => 'required|string|max:2048', 
        ]);

        if ($validaciones->fails()) {
            return response()->json(['errors' => $validaciones->errors()], 422);
        }else{
            $event = new Event();
            $event->nombre = $request->nombre;
            $event->descripcion = $request->descripcion;
            $event->fecha_inicio = $request->fecha_inicio;
            $event->fecha_fin = $request->fecha_fin;
            $event->ubicacion = $request->ubicacion;
            $event->capacidad_max = $request->capacidad_max;

            if ($request->hasFile('ruta_img')) {
                $rutaImg = $request->file('ruta_img')->store('eventos', 'public');
                $event->ruta_img = $rutaImg;
            }

            $event->save();

            return response()->json(['mesasge' =>'evento creado'], 201);
        }
    }
}
