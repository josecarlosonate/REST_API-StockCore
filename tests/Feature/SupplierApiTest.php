<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\Supplier;
use App\Models\User;

class SupplierApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();

        Sanctum::actingAs($user);
    }

    public function test_user_can_list_suppliers(): void
    {
        Supplier::factory()->count(20)->create();

        $response = $this->getJson('/api/v1/suppliers');

        $response->assertOk();

        $response->assertJsonCount(15, 'data');

        $response->assertJsonPath('meta.total', 20);
        $response->assertJsonPath('meta.per_page', 15);
    }

    public function test_user_can_create_supplier(): void
    {
        $payload = [
            'name' => 'TechImport Colombia',
            'tax_id' => '900123456',
            'email' => 'ventas@techimport.com',
            'phone' => '+57 300 123 4567',
            'address' => 'Valledupar, Cesar',
            'is_active' => true
        ];

        $response = $this->postJson('/api/v1/suppliers', $payload);

        $response->assertCreated();

        $response->assertJsonPath('data.name', 'TechImport Colombia');
        $response->assertJsonPath('data.tax_id', '900123456');
        $response->assertJsonPath('data.is_active', true);

        $this->assertDatabaseHas('suppliers', [
            'name' => 'TechImport Colombia',
            'tax_id' => '900123456',
            'is_active' => true,
        ]);
    }

    public function test_supplier_cannot_be_created_with_duplicate_tax_id(): void
    {
        Supplier::factory()->create([
            'tax_id' => '900123456',
        ]);

        $response = $this->postJson('/api/v1/suppliers', [
            'name' => 'Another Supplier',
            'tax_id' => '900123456',
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['tax_id']);
        $this->assertDatabaseCount('suppliers', 1);
    }

    public function test_user_can_partially_update_and_deactivate_supplier(): void
    {
        $supplier = Supplier::factory()->create([
            'name' => 'TechImport Colombia',
            'is_active' => true,
        ]);

        $response = $this->patchJson("/api/v1/suppliers/{$supplier->id}", [
            'phone' => '+57 301 555 1234',
            'is_active' => false,
        ]);

        $response->assertOk();

        $response->assertJsonPath('data.name', 'TechImport Colombia');
        $response->assertJsonPath('data.phone', '+57 301 555 1234');
        $response->assertJsonPath('data.is_active', false);

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'TechImport Colombia',
            'phone' => '+57 301 555 1234',
            'is_active' => false,
        ]);
    }
}
