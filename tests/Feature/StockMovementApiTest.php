<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StockMovementApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        Sanctum::actingAs($this->user);
    }

    public function test_can_create_entry_stock_movement(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create();
        $product->categories()->attach($category->id);
        $product->inventory()->create([
            'quantity' => 10,
        ]);

        $payload = [
            'type' => 'entry',
            'quantity' => 5,
        ];

        $response = $this->postJson("/api/v1/products/{$product->id}/stock-movements", $payload);

        $response->assertCreated();

        $response->assertJsonPath('data.user.id', $this->user->id);
        $response->assertJsonPath('data.user.name', $this->user->name);
        $this->assertDatabaseHas('inventories', [
            'id' => $product->inventory->id,
            'quantity' => 15,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'inventory_id' => $product->inventory->id,
            'user_id' => $this->user->id,
            'type' => 'entry',
            'quantity' => 5,
            'quantity_before' => 10,
            'quantity_after' => 15,
        ]);
    }

    public function test_can_create_exit_stock_movement()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create();
        $product->categories()->attach($category->id);
        $product->inventory()->create([
            'quantity' => 10,
        ]);

        $payload = [
            'type' => 'exit',
            'quantity' => 4,
        ];

        $response = $this->postJson("/api/v1/products/{$product->id}/stock-movements", $payload);

        $response->assertCreated();
        $this->assertDatabaseHas('inventories', [
            'id' => $product->inventory->id,
            'quantity' => 6,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'inventory_id' => $product->inventory->id,
            'type' => 'exit',
            'quantity' => 4,
            'quantity_before' => 10,
            'quantity_after' => 6,
        ]);
    }

    public function test_can_create_adjustment_stock_movement()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create();
        $product->categories()->attach($category->id);
        $product->inventory()->create([
            'quantity' => 10,
        ]);

        $payload = [
            'type' => 'adjustment',
            'quantity' => 7,
            'reason' => 'ajuste',
        ];

        $response = $this->postJson("/api/v1/products/{$product->id}/stock-movements", $payload);

        $response->assertCreated();
        $this->assertDatabaseHas('inventories', [
            'id' => $product->inventory->id,
            'quantity' => 7,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'inventory_id' => $product->inventory->id,
            'type' => 'adjustment',
            'quantity' => 7,
            'quantity_before' => 10,
            'quantity_after' => 7,
            'reason' => 'ajuste',
        ]);
    }

    public function test_cannot_create_exit_when_stock_is_insufficient()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create();
        $product->categories()->attach($category->id);
        $product->inventory()->create([
            'quantity' => 5,
        ]);

        $payload = [
            'type' => 'exit',
            'quantity' => 8,
        ];

        $response = $this->postJson("/api/v1/products/{$product->id}/stock-movements", $payload);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['quantity']);
        $this->assertDatabaseHas('inventories', [
            'id' => $product->inventory->id,
            'quantity' => 5,
        ]);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_adjustment_requires_reason()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create();
        $product->categories()->attach($category->id);
        $product->inventory()->create([
            'quantity' => 10,
        ]);

        $payload = [
            'type' => 'adjustment',
            'quantity' => 7,
        ];

        $response = $this->postJson("/api/v1/products/{$product->id}/stock-movements", $payload);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['reason']);
        $this->assertDatabaseHas('inventories', [
            'id' => $product->inventory->id,
            'quantity' => 10,
        ]);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_can_list_stock_movements_from_different_products(): void
    {
        $productA = Product::factory()->create();
        $productA->inventory()->create(['quantity' => 10]);

        $productB = Product::factory()->create();
        $productB->inventory()->create(['quantity' => 20]);

        $this->postJson("/api/v1/products/{$productA->id}/stock-movements", [
            'type' => 'entry',
            'quantity' => 5,
        ])->assertCreated();

        $this->postJson("/api/v1/products/{$productB->id}/stock-movements", [
            'type' => 'exit',
            'quantity' => 4,
        ])->assertCreated();

        $response = $this->getJson('/api/v1/stock-movements');

        $response->assertOk()->assertJsonCount(2, 'data')->assertJsonFragment([
            'id' => $productA->id,
            'sku' => $productA->sku,
            'name' => $productA->name,
        ])->assertJsonFragment([
            'id' => $productB->id,
            'sku' => $productB->sku,
            'name' => $productB->name,
        ]);
    }

    public function test_can_filter_stock_movements_by_product(): void
    {
        $productA = Product::factory()->create();
        $productA->inventory()->create([
            'quantity' => 10,
        ]);

        $productB = Product::factory()->create();
        $productB->inventory()->create(['quantity' => 20]);

        $this->postJson("/api/v1/products/{$productA->id}/stock-movements", [
            'type' => 'entry',
            'quantity' => 5,
        ])->assertCreated();

        $this->postJson("/api/v1/products/{$productB->id}/stock-movements", [
            'type' => 'exit',
            'quantity' => 4,
        ])->assertCreated();

        $response = $this->getJson("/api/v1/stock-movements?product_id={$productA->id}");

        $response->assertOk()->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.product.id', $productA->id)
            ->assertJsonPath('data.0.type', 'entry')
            ->assertJsonPath('data.0.quantity', 5)
            ->assertJsonPath('data.0.quantity_before', 10)
            ->assertJsonPath('data.0.quantity_after', 15);
    }
}
