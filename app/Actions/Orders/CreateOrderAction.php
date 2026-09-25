<?php

namespace App\Actions\Orders;

use App\Actions\StockMovements\CreateStockMovementAction;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateOrderAction
{
    public function __construct(private CreateStockMovementAction $createStockMovement) {}

    public function execute(User $user, ?Customer $customer, array $items): Order
    {
        return DB::transaction(function () use ($user, $customer, $items) {

            // Obtenemos los productos que forman parte de la orden.
            $products = Product::query()
                ->with('inventory')
                ->whereIn('id', collect($items)->pluck('product_id'))
                ->get()
                ->keyBy('id');

            // Creamos la orden inicialmente con total 0.
            // El total definitivo se calculará a partir de sus items.
            $order = Order::create([
                'customer_id' => $customer?->id,
                'user_id' => $user->id,
                'total' => 0,
            ]);

            $total = 0;
            foreach ($items as $item) {
                $product =  $products->get($item['product_id']);

                // El precio siempre proviene del producto.
                // Nunca confiamos en un precio enviado por el cliente.
                $unitPrice = $product->price;
                $quantity = $item['quantity'];
                $subtotal = round($unitPrice * $quantity, 2);

                // Creamos el detalle conservando el precio histórico
                // utilizado en el momento de la venta.
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;

                // La creación del movimiento se encarga de validar stock,
                // bloquear el inventario y actualizar su cantidad.
                $data = [
                    'type' => 'exit',
                    'quantity' => $quantity,
                    'reason' => "Venta Order #{$order->id}",
                ];
                $this->createStockMovement->execute($product->inventory, $user, $data);
            }
            // Guardamos el total calculado a partir de los detalles.
            $order->update(['total' => round($total, 2)]);

            return $order->load(['customer', 'user', 'items.product']);
        });
    }
}
