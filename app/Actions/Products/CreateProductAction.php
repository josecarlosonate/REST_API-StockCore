<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Support\Facades\DB;


class CreateProductAction
{
    public function execute(array $data): Product
    {
        $categories = $data['categories'];

        unset($data['categories']);

        $product = DB::transaction(function () use ($data, $categories) {

            $product = Product::create($data);

            $product->categories()->attach($categories);

            $product->inventory()->create(['quantity' => 0]);

            return $product;
        });

        return $product;
    }
}
