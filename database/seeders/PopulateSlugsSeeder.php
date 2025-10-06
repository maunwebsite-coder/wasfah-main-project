<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Workshop;
use App\Models\Recipe;

class PopulateSlugsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Populate workshop slugs
        $workshops = Workshop::whereNull('slug')->orWhere('slug', '')->get();
        foreach ($workshops as $workshop) {
            $workshop->slug = $workshop->generateSlug();
            $workshop->save();
        }

        // Populate recipe slugs
        $recipes = Recipe::whereNull('slug')->orWhere('slug', '')->get();
        foreach ($recipes as $recipe) {
            $recipe->slug = $recipe->generateSlug();
            $recipe->save();
        }
    }
}
