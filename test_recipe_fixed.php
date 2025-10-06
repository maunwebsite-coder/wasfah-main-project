<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$recipe = App\Models\Recipe::first();

if($recipe) {
    echo "Recipe ID: " . $recipe->recipe_id . PHP_EOL;
    echo "Image field: " . ($recipe->attributes['image'] ?? 'null') . PHP_EOL;
    echo "Image URL field: " . ($recipe->attributes['image_url'] ?? 'null') . PHP_EOL;
    echo "Image URL accessor: " . ($recipe->image_url ?? 'null') . PHP_EOL;
    
    // Test with a fresh instance
    $freshRecipe = App\Models\Recipe::find($recipe->recipe_id);
    echo "Fresh instance image_url: " . ($freshRecipe->image_url ?? 'null') . PHP_EOL;
} else {
    echo "No recipes found" . PHP_EOL;
}
