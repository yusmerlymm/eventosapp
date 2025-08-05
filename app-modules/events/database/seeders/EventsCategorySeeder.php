<?php

namespace Modules\Events\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Events\Models\EventsCategory;

class EventsCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            "Sociales",
            "Culturales",
            "Corporativos",
            "Academicos",
            "Deportivos",
            "Politicos",
            "Recaudacion"
        ];

        foreach($categories as $category){
            EventsCategory::create([
                "nombre_categoria" => $category
            ]);
        }
    }
}
