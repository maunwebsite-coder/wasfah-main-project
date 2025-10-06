<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ترتيب مهم حتى ما يصير أخطاء
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            RecipeSeeder::class,
            IngredientSeeder::class,
            UserInteractionSeeder::class,
            WorkshopSeeder::class,
            WorkshopBookingSeeder::class,
        ]);
    }
}
