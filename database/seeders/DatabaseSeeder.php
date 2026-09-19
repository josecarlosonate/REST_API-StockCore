<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //usuario administrador
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@stockcore.test',
            'password' => Hash::make('password123'),
        ]);

        //categorias y productos con inventario cero inicialmente
        $categories = Category::factory()->count(5)->create();
        $products = Product::factory()->count(30)->create();

        $products->each(function (Product $product) use ($categories) {

            $randomCategories = $categories->random(random_int(1, 5));
            $categoriesIds = $randomCategories->pluck('id')->toArray();

            $product->categories()->attach($categoriesIds);
            $product->inventory()->create([
                'quantity' => 0,
            ]);
        });
    }
}
