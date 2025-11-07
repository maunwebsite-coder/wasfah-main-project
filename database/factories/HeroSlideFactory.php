<?php

namespace Database\Factories;

use App\Models\HeroSlide;
use Illuminate\Database\Eloquent\Factories\Factory;

class HeroSlideFactory extends Factory
{
    protected $model = HeroSlide::class;

    public function definition(): array
    {
        return [
            'badge' => $this->faker->randomElement(['ورشات', 'وصفات', 'أدوات']),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'image_alt' => $this->faker->sentence(4),
            'desktop_image_path' => null,
            'mobile_image_path' => null,
            'features' => [
                $this->faker->sentence(5),
                $this->faker->sentence(5),
            ],
            'actions' => [
                [
                    'label' => 'اعرف المزيد',
                    'url' => 'https://example.com',
                    'type' => 'primary',
                    'icon' => 'fas fa-arrow-right',
                    'behavior' => 'static',
                    'open_in_new_tab' => false,
                ],
            ],
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
