<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Products\CreateProductAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('categories')->paginate(20);
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request, CreateProductAction $action)
    {
        $data = $request->validated();

        $product = $action->execute($data);

        $product->load('categories');

        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('categories');
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $categories = $data['categories'] ?? null;

        unset($data['categories']);

        DB::transaction(function () use ($data, $categories, $product) {

            $product->update($data);

            if ($categories !== null) {
                $product->categories()->sync($categories);
            }
        });

        $product->load('categories');

        return new ProductResource($product);
    }
}
