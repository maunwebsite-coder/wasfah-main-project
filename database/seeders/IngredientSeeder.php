<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ingredients')->insert([
            // Recipe 1: Tiramisu Brownies
            ['recipe_id' => 1, 'name' => 'دقيق', 'quantity' => '2 كوب', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 1, 'name' => 'كاكاو', 'quantity' => '3 ملاعق كبيرة', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 1, 'name' => 'جبنة ماسكاربوني', 'quantity' => '250 جرام', 'created_at' => now(), 'updated_at' => now()],

            // Recipe 2: Fattoush
            ['recipe_id' => 2, 'name' => 'خس', 'quantity' => '1 رأس', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 2, 'name' => 'طماطم', 'quantity' => '2 حبة', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 2, 'name' => 'خيار', 'quantity' => '2', 'created_at' => now(), 'updated_at' => now()],

            // Recipe 3: Golden Chocolate Tart
            ['recipe_id' => 3, 'name' => 'زبدة', 'quantity' => '150 جرام', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 3, 'name' => 'شوكولاتة داكنة', 'quantity' => '200 جرام', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 3, 'name' => 'طحين', 'quantity' => '200 جرام', 'created_at' => now(), 'updated_at' => now()],

            // Recipe 4: Lemon Yogurt Cake
            ['recipe_id' => 4, 'name' => 'طحين', 'quantity' => '2 كوب', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 4, 'name' => 'زبادي', 'quantity' => '1 كوب', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 4, 'name' => 'عصير ليمون', 'quantity' => 'ربع كوب', 'created_at' => now(), 'updated_at' => now()],

            // Recipe 5: Energy Balls
            ['recipe_id' => 5, 'name' => 'تمر', 'quantity' => '1 كوب', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 5, 'name' => 'مكسرات', 'quantity' => '1 كوب', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 5, 'name' => 'شوفان', 'quantity' => 'نصف كوب', 'created_at' => now(), 'updated_at' => now()],

            // Recipe 6: Chicken Alfredo
            ['recipe_id' => 6, 'name' => 'باستا فيتوتشيني', 'quantity' => '400 جرام', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 6, 'name' => 'صدر دجاج', 'quantity' => '2', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 6, 'name' => 'كريمة طبخ', 'quantity' => '1 كوب', 'created_at' => now(), 'updated_at' => now()],

            // Recipe 7: Classic Beef Burger
            ['recipe_id' => 7, 'name' => 'لحم مفروم', 'quantity' => '500 جرام', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 7, 'name' => 'خبز برجر', 'quantity' => '4', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 7, 'name' => 'جبن شيدر', 'quantity' => '4 شرائح', 'created_at' => now(), 'updated_at' => now()],
            
            // Recipe 8: Lentil Soup
            ['recipe_id' => 8, 'name' => 'عدس أحمر', 'quantity' => '1 كوب', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 8, 'name' => 'بصل', 'quantity' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 8, 'name' => 'كمون', 'quantity' => '1 ملعقة صغيرة', 'created_at' => now(), 'updated_at' => now()],

            // Recipe 9: Greek Salad
            ['recipe_id' => 9, 'name' => 'جبنة فيتا', 'quantity' => '200 جرام', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 9, 'name' => 'خيار', 'quantity' => '2', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 9, 'name' => 'زيتون كلاماتا', 'quantity' => 'نصف كوب', 'created_at' => now(), 'updated_at' => now()],

            // Recipe 10: Chocolate Chip Cookies
            ['recipe_id' => 10, 'name' => 'رقائق شوكولاتة', 'quantity' => '1 كوب', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 10, 'name' => 'سكر بني', 'quantity' => 'نصف كوب', 'created_at' => now(), 'updated_at' => now()],
            ['recipe_id' => 10, 'name' => 'زبدة', 'quantity' => 'نصف كوب', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
