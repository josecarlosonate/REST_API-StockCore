<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class InventoryApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();

        Sanctum::actingAs($user);
    }

    public function test_inventory_is_automatically_created_when_product_is_created(): void
    {

        $categories = Category::factory()->count(2)->create();
        $payload = [
            'name' => 'Laptop Lenovo ThinkPad',
            'description' => 'Laptop para uso empresarial',
            'price' => 3500000,
            'is_active' => true,
            'categories' => $categories->pluck('id')->toArray(),
        ];

        $response = $this->postJson('/api/v1/products', $payload);
        $response->assertCreated();

        $productId = $response->json('data.id');

        $this->assertDatabaseHas('inventories', [
            'product_id' => $productId,
            'quantity'   => 0,
        ]);
    }

    public function test_can_list_inventories(): void
    {
        $products = Product::factory()->count(10)->create();
        foreach ($products as $product) {
            Inventory::create([
                'product_id' => $product->id,
                'quantity' => 0,
            ]);
        }

        $response = $this->getJson('/api/v1/inventories');

        $response->assertOk();
        $response->assertJsonCount(10, 'data');
        $this->assertDatabaseCount('inventories', 10);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'quantity',
                    'min_stock',
                    'max_stock',
                    'product' => [
                        'id',
                        'sku',
                        'name',
                        'slug',
                        'description',
                        'price',
                        'is_active',
                        'categories',
                    ]
                ],
            ],
            'links',
            'meta',
        ]);
    }

    public function test_can_partially_update_inventory(): void
    {
        $product = Product::factory()->create();
        $inventary = $product->inventory()->create([
            'product_id' => $product->id,
            'quantity' => 0,
            'min_stock' => 5
        ]);

        $payload = [
            'max_stock' => 10
        ];

        $response = $this->patchJson("/api/v1/products/$product->id/inventory", $payload);

        $response->assertOk();
        $response->assertJsonPaths([
            'data.id' => $inventary->id,
            'data.max_stock' => 10,
            'data.product.id' => $product->id
        ]);
        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'quantity' => 0,
            'min_stock' => 5,
            'max_stock' => 10
        ]);
    }

    public function test_cannot_update_inventory_with_invalid_stock_range(): void
    {
        $product = Product::factory()->create();
        $product->inventory()->create([
            'product_id' => $product->id,
            'quantity' => 0,
            'min_stock' => 5,
            'max_stock' => 20
        ]);

        $payload = [
            'min_stock' => 30
        ];

        $response = $this->patchJson("/api/v1/products/$product->id/inventory", $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('request');
        $this->assertDatabaseHas('inventories', [
            'product_id' => $product->id,
            'quantity' => 0,
            'min_stock' => 5,
            'max_stock' => 20
        ]);
    }
}
