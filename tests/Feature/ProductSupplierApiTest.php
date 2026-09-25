<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ApiTestCase;

class ProductSupplierApiTest extends ApiTestCase
{
    use RefreshDatabase;

    public function test_can_list_product_suppliers(): void
    {
        $product = Product::factory()->create();

        $supplier = Supplier::factory()->create();

        $product->suppliers()->attach($supplier->id, [
            'supplier_sku' => 'SUP-001',
            'cost' => 85000,
        ]);

        $response = $this->getJson("/api/v1/products/{$product->id}/suppliers");

        $response->assertOk()->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $supplier->id)
            ->assertJsonPath('data.0.name', $supplier->name)
            ->assertJsonPath('data.0.supplier_sku', 'SUP-001')
            ->assertJsonPath('data.0.cost', '85000.00');
    }

    public function test_can_attach_supplier_to_product(): void
    {
        $product = Product::factory()->create();
        $supplier = Supplier::factory()->create();

        $response = $this->postJson("/api/v1/products/{$product->id}/suppliers", [
            'supplier_id' => $supplier->id,
            'supplier_sku' => 'SUP-002',
            'cost' => 85000,
        ]);

        $response->assertCreated()->assertJsonPath('data.id', $supplier->id)
            ->assertJsonPath('data.name', $supplier->name)
            ->assertJsonPath('data.supplier_sku', 'SUP-002')
            ->assertJsonPath('data.cost', '85000.00');

        $this->assertDatabaseHas('product_supplier', [
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'supplier_sku' => 'SUP-002',
            'cost' => 85000,
        ]);
    }

    public function test_cannot_attach_same_supplier_twice_to_product(): void
    {
        $product = Product::factory()->create();
        $supplier = Supplier::factory()->create();

        $product->suppliers()->attach($supplier->id, [
            'supplier_sku' => 'SUP-001',
            'cost' => 80000,
        ]);

        $response = $this->postJson("/api/v1/products/{$product->id}/suppliers", [
            'supplier_id' => $supplier->id,
            'supplier_sku' => 'SUP-002',
            'cost' => 85000,
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['supplier_id']);
        $this->assertDatabaseCount('product_supplier', 1);
    }

    public function test_can_update_product_supplier(): void
    {
        $product = Product::factory()->create();
        $supplier = Supplier::factory()->create();

        $product->suppliers()->attach($supplier->id, [
            'supplier_sku' => 'SUP-001',
            'cost' => 80000,
        ]);

        $response = $this->patchJson("/api/v1/products/{$product->id}/suppliers/{$supplier->id}", [
            'supplier_sku' => 'SUP-UPDATED',
            'cost' => 90000,
        ]);

        $response->assertOk();

        $response->assertJsonPath('data.id', $supplier->id)
            ->assertJsonPath('data.name', $supplier->name)
            ->assertJsonPath('data.supplier_sku', 'SUP-UPDATED')
            ->assertJsonPath('data.cost', '90000.00');

        $this->assertDatabaseHas('product_supplier', [
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'supplier_sku' => 'SUP-UPDATED',
            'cost' => 90000,
        ]);
    }

    public function test_cannot_update_product_supplier_without_fields(): void
    {
        $product = Product::factory()->create();
        $supplier = Supplier::factory()->create();

        $product->suppliers()->attach($supplier->id, [
            'supplier_sku' => 'SUP-001',
            'cost' => 80000,
        ]);

        $response = $this->patchJson("/api/v1/products/{$product->id}/suppliers/{$supplier->id}", []);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['request']);

        $this->assertDatabaseHas('product_supplier', [
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'supplier_sku' => 'SUP-001',
            'cost' => 80000,
        ]);
    }

    public function test_can_set_supplier_sku_to_null(): void
    {
        $product = Product::factory()->create();
        $supplier = Supplier::factory()->create();

        $product->suppliers()->attach($supplier->id, [
            'supplier_sku' => 'SUP-001',
            'cost' => 80000,
        ]);

        $response = $this->patchJson("/api/v1/products/{$product->id}/suppliers/{$supplier->id}", [
            'supplier_sku' => null,
        ]);

        $response->assertOk();

        $response->assertJsonPath('data.id', $supplier->id)
            ->assertJsonPath('data.supplier_sku', null)
            ->assertJsonPath('data.cost', '80000.00');

        $this->assertDatabaseHas('product_supplier', [
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'supplier_sku' => null,
            'cost' => 80000,
        ]);
    }

    public function test_cannot_update_supplier_not_associated_with_product(): void
    {
        $product = Product::factory()->create();
        $supplier = Supplier::factory()->create();

        $response = $this->patchJson("/api/v1/products/{$product->id}/suppliers/{$supplier->id}", [
            'cost' => 90000,
        ]);

        $response->assertNotFound()
            ->assertJson([
                'message' => 'El proveedor no está asociado a este producto.',
                'errors' => [
                    'supplier' => [
                        'El proveedor no está asociado a este producto.',
                    ],
                ],
            ]);
    }

    public function test_can_detach_supplier_from_product(): void
    {
        $product = Product::factory()->create();
        $supplier = Supplier::factory()->create();

        $product->suppliers()->attach($supplier->id, [
            'supplier_sku' => 'SUP-001',
            'cost' => 80000,
        ]);

        $response = $this->deleteJson("/api/v1/products/{$product->id}/suppliers/{$supplier->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('product_supplier', [
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
        ]);
    }
}
