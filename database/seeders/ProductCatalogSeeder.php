<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::factory()->count(5)->create();
        $products = Product::factory()->count(30)->create();
        $suppliers = Supplier::factory()->count(5)->create();

        $products->each(function (Product $product) use ($categories, $suppliers) {
            $this->attachRandomCategories($product, $categories);
            $this->attachRandomSuppliers($product, $suppliers);
            // Todo producto inicializado comienza con un registro de inventario en cero existencias.
            $this->createInventory($product);
        });
    }

    private function attachRandomCategories(Product $product, $categories): void
    {

        $categoryIds = $categories->random(random_int(1, 5))->pluck('id')->toArray();
        $product->categories()->attach($categoryIds);
    }

    private function attachRandomSuppliers(Product $product, $suppliers): void
    {
        $randomSuppliers = $suppliers->random(random_int(1, 3));

        $supplierData = [];

        foreach ($randomSuppliers as $supplier) {
            $supplierData[$supplier->id] = [
                'supplier_sku' => fake()->unique()->bothify('SUP-#####'),
                'cost' => fake()->randomFloat(2, 1000, 100000),
            ];
        }

        $product->suppliers()->attach($supplierData);
    }

    private function createInventory(Product $product): void
    {
        $product->inventory()->create([
            'quantity' => 0,
        ]);
    }
}
