<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@asesco.com'],
            [
                'name'      => 'Administrador',
                'password'  => Hash::make('admin123'),
                'is_active' => true,
            ]
        );

        $admin->assignRole('admin');
    }
}
