<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Recipe with ID 11:\n";

$recipe = App\Models\Recipe::find(11);
if ($recipe) {
    echo "Found recipe: " . $recipe->title . "\n";
    echo "Recipe ID: " . $recipe->recipe_id . "\n";
} else {
    echo "Recipe not found\n";
}

echo "\nAll recipes:\n";
$recipes = App\Models\Recipe::all();
echo "Total recipes: " . $recipes->count() . "\n";

if ($recipes->count() > 0) {
    foreach ($recipes as $recipe) {
        echo "ID: " . $recipe->recipe_id . " - Title: " . $recipe->title . "\n";
    }
}
