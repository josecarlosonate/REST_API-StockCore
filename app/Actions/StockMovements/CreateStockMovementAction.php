<?php

namespace App\Actions\StockMovements;

use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Models\StockMovement;

class CreateStockMovementAction
{
    public function execute(Inventory $inventory, array $data): StockMovement
    {
        return DB::transaction(function () use ($inventory, $data) {

            $lockedInventory = Inventory::query()->whereKey($inventory->id)->lockForUpdate()->firstOrFail();

            $type = StockMovementType::from($data['type']);
            $quantity = $data['quantity'];
            $quantityBefore = $lockedInventory->quantity;

            $quantityAfter = match ($type) {
                StockMovementType::ENTRY => $quantityBefore + $quantity,
                StockMovementType::EXIT => $quantityBefore - $quantity,
                StockMovementType::ADJUSTMENT => $quantity
            };

            // rechazar movimiento por stock insuficiente
            if ($quantityAfter < 0) {
                throw new InsufficientStockException();
            }

            // crear movimiento
            $movement = StockMovement::create([
                'inventory_id' => $lockedInventory->id,
                'type' => $type,
                'quantity' => $quantity,
                'quantity_before' => $quantityBefore,
                'reason' => $data['reason'] ?? null,
            ]);
            // actualizar inventario
            $lockedInventory->update(['quantity' => $quantityAfter]);

            return $movement;
        });
    }
}
