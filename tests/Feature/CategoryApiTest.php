<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ApiTestCase;

class CategoryApiTest extends ApiTestCase
{
    use RefreshDatabase;

    public function test_can_list_categories(): void
    {
        Category::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'is_active',
                ],
            ],
        ]);
    }

    public function test_can_show_category(): void
    {

        $category = Category::factory()->create();

        $response = $this->getJson("/api/v1/categories/{$category->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $category->id);
        $response->assertJsonPath('data.name', $category->name);
        $response->assertJsonPath('data.slug', $category->slug);
        $response->assertJsonPath('data.is_active', $category->is_active);
    }

    public function test_returns_404_when_category_does_not_exist(): void
    {
        $response = $this->getJson('/api/v1/categories/9000');

        $response->assertNotFound();
    }

    public function test_can_create_category(): void
    {
        $payload = [
            'name' => 'Computadores',
            'description' => 'Todos las marcas de computadores',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/categories', $payload);

        $response->assertCreated();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'slug',
                'description',
                'is_active',
            ],
        ]);
        $response->assertJsonFragment([
            'name' => 'Computadores',
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('categories', [
            'id' => $response->json('data.id'),
            'name' => 'Computadores',
            'is_active' => true,
        ]);
    }

    public function test_cannot_create_category_without_name(): void
    {
        $payload = [
            'description' => 'Todos las marcas de computadores',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/categories', $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name']);
        $this->assertDatabaseMissing('categories', [
            'description' => 'Todos las marcas de computadores',
        ]);
    }

    public function test_cannot_create_category_with_duplicate_name(): void
    {
        Category::factory()->create([
            'name' => 'Computadores',
        ]);

        $payload = [
            'name' => 'Computadores',
            'description' => 'Todos las marcas de computadores',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/categories', $payload);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name']);
        $this->assertDatabaseCount('categories', 1);
    }

    public function test_can_update_category_partially(): void
    {
        $category = Category::factory()->create([
            'name' => 'Computadores',
            'description' => 'Todos las marcas de computadores',
        ]);

        $payload = [
            'description' => 'Ultra mega rapidos computadores',
        ];

        $response = $this->patchJson("/api/v1/categories/{$category->id}", $payload);

        $response->assertOk();
        $response->assertJsonPath('data.description', 'Ultra mega rapidos computadores');
        $response->assertJsonPath('data.name', 'Computadores');
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Computadores',
            'description' => 'Ultra mega rapidos computadores',
        ]);
    }

    public function test_updating_category_name_regenerates_slug(): void
    {
        $category = Category::factory()->create([
            'name' => 'Computadores',
            'description' => 'Todos las marcas de computadores',
        ]);

        $slugOld = $category->slug;

        $payload = [
            'name' => 'Mis computadores',
        ];

        $response = $this->patchJson("/api/v1/categories/{$category->id}", $payload);

        $response->assertOk();

        $response->assertJsonPath('data.name', 'Mis computadores');
        $response->assertJsonPath('data.slug', 'mis-computadores');
        $this->assertNotEquals($slugOld, $response->json('data.slug'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Mis computadores',
            'slug' => 'mis-computadores',
        ]);
    }

    public function test_can_list_products_by_category(): void
    {
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();

        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $product3 = Product::factory()->create();

        $categoryA->products()->attach([$product1->id, $product2->id]);
        $categoryB->products()->attach($product3->id);

        $response = $this->getJson("/api/v1/categories/{$categoryA->id}/products");

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
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
        $response->assertJsonFragment([
            'id' => $product1->id,
        ]);

        $response->assertJsonFragment([
            'id' => $product2->id,
        ]);

        $response->assertJsonMissing([
            'id' => $product3->id,
        ]);
    }
}
