<?php

$base = require __DIR__ . '/baking_tips.php';

return array_replace_recursive($base, [
    'meta' => [
        'title' => 'Dessert making tips – Wasfah',
    ],
    'hero' => [
        'title' => 'Dessert making tips',
        'subtitle' => 'Expert-backed tips and practical guidance to help you craft beautiful, delicious desserts.',
    ],
    'categories' => [
        [
            'title' => 'Essential dessert tips',
        ],
        [
            'title' => 'Temperature guidance',
        ],
        [
            'title' => 'Ingredient handling',
        ],
    ],
    'tools' => [
        'title' => 'Essential dessert tools',
    ],
]);
