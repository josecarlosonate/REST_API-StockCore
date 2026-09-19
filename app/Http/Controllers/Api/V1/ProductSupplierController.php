<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductSupplierRequest;
use App\Http\Resources\ProductSupplierResource;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductSupplierController extends Controller
{
    public function index(Product $product)
    {
        $suppliers = $product->suppliers()->get();

        return ProductSupplierResource::collection($suppliers);
    }

    public function store(StoreProductSupplierRequest $request, Product $product)
    {
        $validated = $request->validated();

        $product->suppliers()->attach($validated['supplier_id'], [
            'supplier_sku' => $validated['supplier_sku'] ?? null,
            'cost' => $validated['cost'],
        ]);

        return response()->json([
            'message' => 'Proveedor asociado al producto correctamente.',
            'supplier_id' => $validated['supplier_id'],
        ], 201);
    }

    public function update(Request $request, Product $product, Supplier $supplier)
    {
        $data = $request->validate([
            'supplier_sku' => ['sometimes', 'nullable', 'string', 'max:255'],
            'cost' => ['sometimes', 'required', 'numeric', 'min:0', 'max:100000000']
        ]);

        if (empty($data)) {
            return response()->json([
                'message' => 'Debe proporcionar al menos un campo para actualizar.',
            ], 422);
        }

        $exists = $product->suppliers()->whereKey($supplier->id)->exists();

        if (!$exists) {
            return response()->json([
                'message' => 'El proveedor no está asociado a este producto.',
            ], 404);
        }

        $product->suppliers()->updateExistingPivot($supplier->id, $data);

        return response()->json([
            'message' => 'Relación producto-proveedor actualizada correctamente.',
            'supplier_id' => $supplier->id,
        ]);
    }

    public function destroy(Product $product, Supplier $supplier)
    {
        $detached = $product->suppliers()->detach($supplier->id);

        if ($detached === 0) {
            return response()->json([
                'message' => 'El proveedor no está asociado a este producto.',
            ], 404);
        }

        return response()->json([
            'message' => 'Proveedor desasociado del producto correctamente.',
            'supplier_id' => $supplier->id,
        ]);
    }
}
