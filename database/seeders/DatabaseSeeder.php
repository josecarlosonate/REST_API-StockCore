<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = Category::factory()->count(5)->create();
        $products = Product::factory()->count(30)->create();

        $products->each(function (Product $product) use ($categories) {

            $randomCategories = $categories->random(random_int(1, 5));
            $categoriesIds = $randomCategories->pluck('id')->toArray();

            $product->categories()->attach($categoriesIds);
        });
    }
}
