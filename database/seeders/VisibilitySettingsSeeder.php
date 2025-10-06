<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VisibilitySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            [
                'section' => 'header',
                'is_visible' => true,
                'description' => 'Website header section',
                'page_name' => 'header',
                'section_name' => 'header',
                'element_key' => 'header'
            ],
            [
                'section' => 'navigation',
                'is_visible' => true,
                'description' => 'Main navigation menu',
                'page_name' => 'navigation',
                'section_name' => 'navigation',
                'element_key' => 'navigation'
            ],
            [
                'section' => 'footer',
                'is_visible' => true,
                'description' => 'Website footer section',
                'page_name' => 'footer',
                'section_name' => 'footer',
                'element_key' => 'footer'
            ],
            [
                'section' => 'sidebar',
                'is_visible' => true,
                'description' => 'Sidebar section',
                'page_name' => 'sidebar',
                'section_name' => 'sidebar',
                'element_key' => 'sidebar'
            ],
            [
                'section' => 'search',
                'is_visible' => true,
                'description' => 'Search functionality',
                'page_name' => 'search',
                'section_name' => 'search',
                'element_key' => 'search'
            ],
            [
                'section' => 'recipes',
                'is_visible' => true,
                'description' => 'Recipes section',
                'page_name' => 'recipes',
                'section_name' => 'recipes',
                'element_key' => 'recipes'
            ],
            [
                'section' => 'tools',
                'is_visible' => true,
                'description' => 'Tools section',
                'page_name' => 'tools',
                'section_name' => 'tools',
                'element_key' => 'tools'
            ],
            [
                'section' => 'workshops',
                'is_visible' => true,
                'description' => 'Workshops section',
                'page_name' => 'workshops',
                'section_name' => 'workshops',
                'element_key' => 'workshops'
            ],
            [
                'section' => 'notifications',
                'is_visible' => true,
                'description' => 'Notifications section',
                'page_name' => 'notifications',
                'section_name' => 'notifications',
                'element_key' => 'notifications'
            ],
            [
                'section' => 'profile',
                'is_visible' => true,
                'description' => 'User profile section',
                'page_name' => 'profile',
                'section_name' => 'profile',
                'element_key' => 'profile'
            ],
            [
                'section' => 'admin',
                'is_visible' => false,
                'description' => 'Admin panel section',
                'page_name' => 'admin',
                'section_name' => 'admin',
                'element_key' => 'admin'
            ]
        ];

        foreach ($defaultSettings as $setting) {
            \App\Models\VisibilitySetting::updateOrCreate(
                ['section' => $setting['section']],
                $setting
            );
        }
    }
}
