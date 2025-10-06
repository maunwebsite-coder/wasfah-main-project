<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            // IDs will be auto-incremented starting from 1
            ['name' => 'حلويات', 'created_at' => now(), 'updated_at' => now()],         // Corresponds to category_id: 1
            ['name' => 'سلطات', 'created_at' => now(), 'updated_at' => now()],          // Corresponds to category_id: 2
            ['name' => 'سناكات صحية', 'created_at' => now(), 'updated_at' => now()],   // Corresponds to category_id: 3
            ['name' => 'أطباق رئيسية', 'created_at' => now(), 'updated_at' => now()],  // Corresponds to category_id: 4
            ['name' => 'شوربات', 'created_at' => now(), 'updated_at' => now()],        // Corresponds to category_id: 5
        ]);
    }
}
