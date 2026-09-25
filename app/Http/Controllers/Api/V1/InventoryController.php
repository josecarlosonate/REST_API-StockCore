<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateInventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;

class InventoryController extends Controller
{
    public function index()
    {
        Gate::authorize('inventory.view');

        $inventories = Inventory::with('product.categories')->paginate(15);

        return InventoryResource::collection($inventories);
    }

    public function show(Product $product)
    {
        Gate::authorize('inventory.view');

        $inventory = $product->inventory()->with('product.categories')->firstOrFail();

        return new InventoryResource($inventory);
    }

    public function update(UpdateInventoryRequest $request, Product $product)
    {
        $data = $request->validated();
        $inventory = $product->inventory;
        $inventory->update($data);
        $inventory->load('product.categories');

        return new InventoryResource($inventory);
    }
}
