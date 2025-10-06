<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء مستخدم مدير
        User::updateOrCreate(
            ['email' => 'admin@wasfah.com'],
            [
                'name' => 'مدير النظام',
                'email' => 'admin@wasfah.com',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );
    }
}
