<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // usuario administrador
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@stockcore.test',
            'password' => Hash::make('password123'),
        ]);

        // categorias y productos con inventario cero inicialmente
        $categories = Category::factory()->count(5)->create();
        $products = Product::factory()->count(30)->create();
        $suppliers = Supplier::factory()->count(5)->create();

        $products->each(function (Product $product) use ($categories, $suppliers) {
            $randomCategories = $categories->random(random_int(1, 5));
            $categoriesIds = $randomCategories->pluck('id')->toArray();

            $product->categories()->attach($categoriesIds);

            $randomSuppliers = $suppliers->random(random_int(1, 3));

            $supplierData = [];

            foreach ($randomSuppliers as $supplier) {
                $supplierData[$supplier->id] = [
                    'supplier_sku' => fake()->unique()->bothify('SUP-#####'),
                    'cost' => fake()->randomFloat(2, 1000, 100000),
                ];
            }

            $product->suppliers()->attach($supplierData);

            $product->inventory()->create([
                'quantity' => 0,
            ]);
        });
    }
}
