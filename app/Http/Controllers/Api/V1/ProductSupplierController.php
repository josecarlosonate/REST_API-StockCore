<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductSupplierRequest;
use App\Http\Requests\UpdateProductSupplierRequest;
use App\Http\Resources\ProductSupplierResource;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\Gate;

class ProductSupplierController extends Controller
{
    public function index(Product $product)
    {
        Gate::authorize('suppliers.view');

        $suppliers = $product->suppliers()->get();

        return ProductSupplierResource::collection($suppliers);
    }

    public function store(StoreProductSupplierRequest $request, Product $product)
    {
        $data = $request->validated();

        $product->suppliers()->attach($data['supplier_id'], [
            'supplier_sku' => $data['supplier_sku'] ?? null,
            'cost' => $data['cost'],
        ]);

        $supplier = $product->suppliers()->whereKey($data['supplier_id'])->firstOrFail();

        return (new ProductSupplierResource($supplier))->response()->setStatusCode(201);
    }

    public function update(UpdateProductSupplierRequest $request, Product $product, Supplier $supplier)
    {
        $data = $request->validated();

        $exists = $product->suppliers()->whereKey($supplier->id)->exists();

        if (! $exists) {
            return response()->json([
                'message' => 'El proveedor no está asociado a este producto.',
                'errors' => [
                    'supplier' => [
                        'El proveedor no está asociado a este producto.',
                    ],
                ],
            ], 404);
        }

        $product->suppliers()->updateExistingPivot($supplier->id, $data);

        $updatedSupplier = $product->suppliers()->whereKey($supplier->id)->firstOrFail();

        return new ProductSupplierResource($updatedSupplier);
    }

    public function destroy(Product $product, Supplier $supplier)
    {
        Gate::authorize('product-suppliers.manage');

        $detached = $product->suppliers()->detach($supplier->id);

        if ($detached === 0) {
            return response()->json([
                'message' => 'El proveedor no está asociado a este producto.',
                'errors' => [
                    'supplier' => [
                        'El proveedor no está asociado a este producto.',
                    ],
                ],
            ], 404);
        }

        return response()->noContent();
    }
}
