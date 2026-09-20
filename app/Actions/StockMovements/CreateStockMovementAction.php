<?php

namespace App\Actions\StockMovements;

use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Models\Inventory;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateStockMovementAction
{
    public function execute(Inventory $inventory, User $user, array $data): StockMovement
    {
        return DB::transaction(function () use ($inventory, $user, $data) {

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
                throw new InsufficientStockException;
            }

            // crear movimiento
            $movement = StockMovement::create([
                'inventory_id' => $lockedInventory->id,
                'user_id' => $user->id,
                'type' => $type,
                'quantity' => $quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'reason' => $data['reason'] ?? null,
            ]);
            // actualizar inventario
            $lockedInventory->update(['quantity' => $quantityAfter]);

            return $movement;
        });
    }
}
