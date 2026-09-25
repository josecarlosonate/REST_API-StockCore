<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Products
            'products.view',
            'products.create',
            'products.update',
            'products.delete',

            // Categories
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',

            // Suppliers
            'suppliers.view',
            'suppliers.create',
            'suppliers.update',
            'suppliers.delete',

            // Customers
            'customers.view',
            'customers.create',
            'customers.update',
            'customers.delete',

            // Inventory
            'inventory.view',
            'inventory.adjust',

            // Stock movements
            'stock-movements.view',
            'stock-movements.create',

            // Orders
            'orders.view',
            'orders.create',
        ];

        $roles = [
            'seller' => [
                'products.view',
                'customers.view',
                'customers.create',
                'customers.update',
                'inventory.view',
                'orders.view',
                'orders.create',
            ],

            'warehouse' => [
                'products.view',
                'inventory.view',
                'inventory.adjust',
                'stock-movements.view',
                'stock-movements.create',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $admin = Role::findOrCreate('admin');
        $admin->syncPermissions(Permission::all());

        foreach ($roles as $roleName => $permissions) {
            $role = Role::findOrCreate($roleName);
            $role->syncPermissions($permissions);
        }
    }
}
