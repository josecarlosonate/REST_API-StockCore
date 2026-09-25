<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ApiTestCase;

class ProductApiTest extends ApiTestCase
{
    use RefreshDatabase;

    public function test_can_list_products(): void
    {
        Product::factory()->count(10)->create();

        $response = $this->getJson('/api/v1/products');

        $response->assertOk();
        $response->assertJsonCount(10, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'sku',
                    'name',
                    'slug',
                    'description',
                    'price',
                    'is_active',
                    'categories',
                ],
            ],
            'links',
            'meta',
        ]);
    }

    public function test_can_show_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/v1/products/{$product->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $product->id);
        $response->assertJsonPath('data.sku', $product->sku);
        $response->assertJsonPath('data.name', $product->name);
        $response->assertJsonPath('data.slug', $product->slug);
    }

    public function test_returns_404_when_product_does_not_exist(): void
    {
        $response = $this->getJson('/api/v1/products/999999');

        $response->assertNotFound();
    }

    public function test_can_create_product(): void
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

        $this->assertDatabaseHas('products', [
            'name' => 'Laptop Lenovo ThinkPad',
            'description' => 'Laptop para uso empresarial',
            'price' => 3500000,
            'is_active' => true,
        ]);

        $productId = $response->json('data.id');
        foreach ($categories as $category) {
            $this->assertDatabaseHas('category_product', [
                'category_id' => $category->id,
                'product_id' => $productId,
            ]);
        }
    }

    public function test_cannot_create_product_without_name(): void
    {
        $category = Category::factory()->create();

        $payload = [
            'description' => 'Laptop para uso empresarial',
            'price' => 3500000,
            'is_active' => true,
            'categories' => [$category->id],
        ];

        $response = $this->postJson('/api/v1/products', $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_cannot_create_product_with_nonexistent_category(): void
    {
        $payload = [
            'name' => 'Laptop Lenovo ThinkPad',
            'description' => 'Laptop para uso empresarial',
            'price' => 3500000,
            'is_active' => true,
            'categories' => [99999],
        ];

        $response = $this->postJson('/api/v1/products', $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['categories.0']);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_can_update_product_partially(): void
    {
        $product = Product::factory()->create([
            'name' => 'Laptop Lenovo ThinkPad',
            'price' => 3500000,
        ]);

        $response = $this->patchJson("/api/v1/products/{$product->id}", [
            'price' => 4000000,
        ]);

        $response->assertOk();

        $response->assertJsonPath('data.price', '4000000.00');
        $response->assertJsonPath('data.name', 'Laptop Lenovo ThinkPad');

        $product->refresh();

        $this->assertEquals('4000000.00', $product->price);
        $this->assertEquals('Laptop Lenovo ThinkPad', $product->name);
    }

    public function test_updating_product_name_regenerates_slug_but_preserves_sku(): void
    {
        $product = Product::factory()->create([
            'name' => 'Laptop Lenovo ThinkPad',
        ]);

        $originalSku = $product->sku;
        $originalSlug = $product->slug;

        $response = $this->patchJson("/api/v1/products/{$product->id}", [
            'name' => 'Laptop Lenovo Legion',
        ]);

        $response->assertOk();

        $product->refresh();

        $this->assertEquals($originalSku, $product->sku);
        $this->assertNotEquals($originalSlug, $product->slug);
    }
}
