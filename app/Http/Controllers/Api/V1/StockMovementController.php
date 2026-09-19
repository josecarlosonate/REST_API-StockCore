<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockMovementRequest;
use App\Models\Product;
use App\Actions\StockMovements\CreateStockMovementAction;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;

class StockMovementController extends Controller
{
    public function index(Product $product)
    {
        $stockMovements = $product->inventory->stockMovements()->latest()->paginate(15);
        return StockMovementResource::collection($stockMovements);
    }

    public function store(
        StoreStockMovementRequest $request,
        Product $product,
        CreateStockMovementAction $action
    ) {
        $data = $request->validated();
        $movement = $action->execute($product->inventory, $data);
        return (new StockMovementResource($movement))->response()->setStatusCode(201);
    }
}
