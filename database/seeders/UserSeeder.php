<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@facturation.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create default employe user
        User::create([
            'name' => 'Employé',
            'email' => 'employe@facturation.com',
            'password' => Hash::make('employe123'),
            'role' => 'employe',
            'is_active' => true,
        ]);
    }
}

