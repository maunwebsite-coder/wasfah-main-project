<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Ingredient;

class TestRecipeUpdate extends Command
{
    protected $signature = 'test:recipe-update';
    protected $description = 'Test recipe update functionality';

    public function handle()
    {
        $this->info('Testing recipe update functionality...');
        
        // Check if recipe 12 exists
        $recipe = Recipe::find(12);
        if (!$recipe) {
            $this->error('Recipe 12 not found. Creating a test recipe...');
            
            // Get first category
            $category = Category::first();
            if (!$category) {
                $this->error('No categories found. Please run category seeder first.');
                return;
            }
            
            // Create test recipe
            $recipe = Recipe::create([
                'title' => 'وصفة اختبار للتحديث',
                'description' => 'وصفة اختبار للتحديث',
                'author' => 'مدير النظام',
                'prep_time' => 30,
                'cook_time' => 60,
                'servings' => 4,
                'difficulty' => 'medium',
                'category_id' => $category->category_id,
                'steps' => ['خطوة 1', 'خطوة 2'],
                'tools' => [],
            ]);
            
            $this->info('Created test recipe with ID: ' . $recipe->id);
        } else {
            $this->info('Found recipe 12: ' . $recipe->title);
        }
        
        // Test update
        $this->info('Testing recipe update...');
        $originalTitle = $recipe->title;
        
        try {
            $recipe->update([
                'title' => $originalTitle . ' - تم التحديث',
                'description' => $recipe->description . ' - تم التحديث',
            ]);
            
            $this->info('Recipe updated successfully!');
            $this->info('New title: ' . $recipe->fresh()->title);
            
            // Revert changes
            $recipe->update([
                'title' => $originalTitle,
                'description' => $recipe->description,
            ]);
            
            $this->info('Changes reverted successfully.');
            
        } catch (\Exception $e) {
            $this->error('Error updating recipe: ' . $e->getMessage());
        }
        
        $this->info('Test completed.');
    }
}