<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockMovementRequest;
use App\Models\Product;
use App\Actions\StockMovements\CreateStockMovementAction;
use App\Http\Resources\StockMovementResource;

class StockMovementController extends Controller
{
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
