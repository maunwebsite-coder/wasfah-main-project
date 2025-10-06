<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserInteraction;

class UserInteractionSeeder extends Seeder
{
    public function run(): void
    {
        // 🟢 User 1 (Abdullah Dawood) - Interacts with 6 recipes
        UserInteraction::storeOrUpdate(1, 1, [ 'is_saved' => 1, 'is_made'  => 1, 'rating'   => 5 ]);
        UserInteraction::storeOrUpdate(1, 2, [ 'is_saved' => 0, 'is_made'  => 1, 'rating'   => 4 ]);
        UserInteraction::storeOrUpdate(1, 4, [ 'is_saved' => 1, 'is_made'  => 0, 'rating'   => null ]);
        UserInteraction::storeOrUpdate(1, 5, [ 'is_saved' => 1, 'is_made'  => 0, 'rating'   => 4 ]);
        UserInteraction::storeOrUpdate(1, 7, [ 'is_saved' => 0, 'is_made'  => 1, 'rating'   => 3 ]);
        UserInteraction::storeOrUpdate(1, 9, [ 'is_saved' => 1, 'is_made'  => 1, 'rating'   => 4 ]);

        // 🟢 User 2 (Mohammed Ali) - Interacts with 6 recipes
        UserInteraction::storeOrUpdate(2, 1, [ 'is_saved' => 1, 'is_made'  => 1, 'rating'   => 4 ]);
        UserInteraction::storeOrUpdate(2, 2, [ 'is_saved' => 0, 'is_made'  => 1, 'rating'   => 2 ]);
        UserInteraction::storeOrUpdate(2, 3, [ 'is_saved' => 0, 'is_made'  => 1, 'rating'   => 5 ]);
        UserInteraction::storeOrUpdate(2, 5, [ 'is_saved' => 1, 'is_made'  => 0, 'rating'   => null ]);
        UserInteraction::storeOrUpdate(2, 8, [ 'is_saved' => 1, 'is_made'  => 0, 'rating'   => 4 ]);
        UserInteraction::storeOrUpdate(2, 10, [ 'is_saved' => 0, 'is_made' => 1, 'rating'   => 5 ]);

        // 🟢 User 3 (Abdulrahman) - Interacts with 6 recipes
        UserInteraction::storeOrUpdate(3, 3, [ 'is_saved' => 1, 'is_made'  => 0, 'rating'   => 3 ]);
        UserInteraction::storeOrUpdate(3, 4, [ 'is_saved' => 1, 'is_made'  => 1, 'rating'   => 5 ]);
        UserInteraction::storeOrUpdate(3, 6, [ 'is_saved' => 1, 'is_made'  => 0, 'rating'   => 3 ]);
        UserInteraction::storeOrUpdate(3, 7, [ 'is_saved' => 0, 'is_made'  => 1, 'rating'   => 4 ]);
        UserInteraction::storeOrUpdate(3, 8, [ 'is_saved' => 1, 'is_made'  => 1, 'rating'   => 4 ]);
        UserInteraction::storeOrUpdate(3, 9, [ 'is_saved' => 0, 'is_made'  => 1, 'rating'   => 4 ]);

        // 🟢 User 4 (Ahmed) - Interacts with 6 recipes
        UserInteraction::storeOrUpdate(4, 1, [ 'is_saved' => 0, 'is_made'  => 1, 'rating'   => 5 ]);
        UserInteraction::storeOrUpdate(4, 2, [ 'is_saved' => 1, 'is_made'  => 0, 'rating'   => 4 ]);
        UserInteraction::storeOrUpdate(4, 5, [ 'is_saved' => 1, 'is_made'  => 1, 'rating'   => 5 ]);
        UserInteraction::storeOrUpdate(4, 6, [ 'is_saved' => 0, 'is_made'  => 1, 'rating'   => 5 ]);
        UserInteraction::storeOrUpdate(4, 9, [ 'is_saved' => 1, 'is_made'  => 0, 'rating'   => null ]);
        UserInteraction::storeOrUpdate(4, 10, [ 'is_saved' => 1, 'is_made' => 1, 'rating'   => 5 ]);
    }
}

