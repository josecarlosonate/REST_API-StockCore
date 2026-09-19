<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;

class StockMovementApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();

        Sanctum::actingAs($user);
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
            'quantity' => 5
        ];

        $response = $this->postJson("/api/v1/products/{$product->id}/stock-movements", $payload);

        $response->assertCreated();
        $this->assertDatabaseHas('inventories', [
            'id' => $product->inventory->id,
            'quantity' => 15,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'inventory_id' => $product->inventory->id,
            'type' => 'entry',
            'quantity' => 5,
            'quantity_before' => 10
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
            'quantity' => 4
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
            'quantity_before' => 10
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
            'reason' => 'ajuste'
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
            'reason' => 'ajuste'
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
            'quantity' => 8
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
            'quantity' => 7
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
}
