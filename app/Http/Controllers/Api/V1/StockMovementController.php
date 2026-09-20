<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockMovementRequest;
use App\Models\Product;
use App\Actions\StockMovements\CreateStockMovementAction;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Enums\StockMovementType;

class StockMovementController extends Controller
{
    public function index(Product $product)
    {
        $stockMovements = $product->inventory->stockMovements()->latest()->paginate(15);
        return StockMovementResource::collection($stockMovements);
    }

    public function listMovements(Request $request)
    {
        $data = $request->validate([
            'type' => [
                'nullable',
                Rule::enum(StockMovementType::class),
            ],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
        ]);

        $query = StockMovement::query()->with('inventory.product');

        if (isset($data['type'])) {
            $query->where('type', $data['type']);
        }

        if (isset($data['product_id'])) {
            $query->whereHas('inventory', function ($query) use ($data) {
                $query->where('product_id', $data['product_id']);
            });
        }

        $movements = $query
            ->latest()
            ->paginate(15);

        return StockMovementResource::collection($movements);
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
