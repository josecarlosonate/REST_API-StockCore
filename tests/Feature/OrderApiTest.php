<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        Sanctum::actingAs($this->user);
    }

    public function test_authenticated_user_can_list_orders(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'total' => 100000,
        ]);

        $response = $this->getJson('/api/v1/orders');

        $response->assertOk();
        $response->assertJsonPath('data.0.id', $order->id);
    }

    public function test_authenticated_user_can_view_an_order(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'total' => 50000,
        ]);

        $response = $this->getJson("/api/v1/orders/{$order->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $order->id);
        $response->assertJsonPath('data.total', '50000.00');
    }

    public function test_show_returns_404_for_non_existent_order(): void
    {
        $this->getJson('/api/v1/orders/99999')
            ->assertNotFound();
    }

    public function test_authenticated_user_can_create_an_order(): void
    {
        $customer = Customer::factory()->create();

        $product = Product::factory()->create(['price' => 10000]);

        $inventory = Inventory::create([
            'product_id' => $product->id,
            'quantity' => 50,
        ]);

        $payload = [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/orders', $payload);

        $response->assertCreated();

        $response->assertJsonPath('data.total', 20000)
            ->assertJsonPath('data.customer.id', $customer->id)
            ->assertJsonPath('data.user.id', $this->user->id)
            ->assertJsonCount(1, 'data.items');
        $response->assertJsonPath('data.items.0.product.id', $product->id)
            ->assertJsonPath('data.items.0.product.sku', $product->sku)
            ->assertJsonPath('data.items.0.quantity', 2)
            ->assertJsonPath('data.items.0.unit_price', '10000.00')
            ->assertJsonPath('data.items.0.subtotal', '20000.00');

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'customer_id' => $customer->id,
            'total' => 20000,
        ]);

        // Verificar que se descontó el stock
        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'quantity' => 48,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'inventory_id' => $inventory->id,
            'user_id' => $this->user->id,
            'type' => 'exit',
            'quantity' => 2,
            'quantity_before' => 50,
            'quantity_after' => 48,
        ]);
    }

    public function test_can_create_order_without_customer(): void
    {
        $product = Product::factory()->create(['price' => 15000]);
        Inventory::create([
            'product_id' => $product->id,
            'quantity' => 10,
        ]);

        $payload = [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $this->postJson('/api/v1/orders', $payload)
            ->assertCreated()
            ->assertJsonPath('data.customer', null);
    }

    public function test_cannot_create_order_with_insufficient_stock(): void
    {
        $product = Product::factory()->create(['price' => 10000]);
        Inventory::create([
            'product_id' => $product->id,
            'quantity' => 1, // solo 1 disponible
        ]);

        $payload = [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5, // pide 5
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/orders', $payload);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors('quantity');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);

        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_cannot_create_order_with_invalid_data(): void
    {
        $this->postJson('/api/v1/orders', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }

    public function test_cannot_create_order_with_duplicate_products(): void
    {
        $product = Product::factory()->create();

        $payload = [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
                ['product_id' => $product->id, 'quantity' => 2], // duplicado
            ],
        ];

        $response = $this->postJson('/api/v1/orders', $payload);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['items.0.product_id']);
    }

    public function test_order_keeps_historical_product_price(): void
    {
        $product = Product::factory()->create([
            'price' => 10000,
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'quantity' => 10,
        ]);

        $payload = [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/orders', $payload);

        $response->assertCreated();

        $product->update([
            'price' => 15000,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'price' => 15000,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 10000,
            'subtotal' => 20000,
        ]);
    }
}
