<?php

namespace Modules\Events\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Events\Models\EventsType;
use Modules\Events\Models\EventsCategory;

class EventsTypeController
{
    public function getTypesByCategory($categoryId)
    {
        $category = EventsCategory::where('id',$categoryId)->first();

        if (!$category) {
            return response()->json([], 404);
        }

        // ⚠️ Paso 2: Usar la relación definida en el modelo
        // Esto es una forma más robusta y "laravelesca" de hacerlo.
        $types = $category->types()->get(['id', 'nombre_tipo_evento']);

        return response()->json($types);
        // $types = EventsType::where('id_categoria', $categoryId)->get(['id', 'nombre_tipo_evento']);
        // return response()->json($types);
    }
}
