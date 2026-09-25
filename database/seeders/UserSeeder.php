<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@stockcore.test',
            'password' => Hash::make('password123'),
        ]);

        $admin->assignRole('admin');

        $seller = User::factory()->create([
            'name' => 'Seller',
            'email' => 'seller@stockcore.test',
            'password' => Hash::make('password123'),
        ]);

        $seller->assignRole('seller');

        $warehouse = User::factory()->create([
            'name' => 'Warehouse',
            'email' => 'warehouse@stockcore.test',
            'password' => Hash::make('password123'),
        ]);

        $warehouse->assignRole('warehouse');
    }
}
