<?php

namespace Modules\Events\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Events\Models\EventsCategory;

class EventsCategoryController
{
    public function AvailableCategories()
    {
        $categories = EventsCategory::all();
        return response()->json($categories);
    }
}
