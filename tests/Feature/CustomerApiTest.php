<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ApiTestCase;

class CustomerApiTest extends ApiTestCase
{
    use RefreshDatabase;

    public function test_can_list_customers(): void
    {
        Customer::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/customers');

        $response->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_can_create_customer_with_minimum_data(): void
    {
        $response = $this->postJson('/api/v1/customers', [
            'name' => 'Carlos Pérez',
            'phone' => '3001234567',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Carlos Pérez')
            ->assertJsonPath('data.phone', '3001234567')
            ->assertJsonPath('data.is_active', true);

        $this->assertDatabaseHas('customers', [
            'name' => 'Carlos Pérez',
            'phone' => '3001234567',
            'is_active' => true,
        ]);
    }

    public function test_cannot_create_customer_with_incomplete_document(): void
    {
        $response = $this->postJson('/api/v1/customers', [
            'name' => 'María Rodríguez',
            'document_type' => 'CC',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('document_number');

        $this->assertDatabaseMissing('customers', [
            'name' => 'María Rodríguez',
        ]);
    }

    public function test_can_partially_update_customer_without_losing_existing_data(): void
    {
        $customer = Customer::factory()->create([
            'document_type' => 'CC',
            'document_number' => '123456789',
            'phone' => '3001111111',
        ]);

        $response = $this->patchJson("/api/v1/customers/{$customer->id}", [
            'phone' => '3009999999',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.phone', '3009999999')
            ->assertJsonPath('data.document_type', 'CC')
            ->assertJsonPath('data.document_number', '123456789');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'phone' => '3009999999',
            'document_type' => 'CC',
            'document_number' => '123456789',
        ]);
    }

    public function test_cannot_leave_customer_with_incomplete_document_after_update(): void
    {
        $customer = Customer::factory()->create([
            'document_type' => 'CC',
            'document_number' => '123456789',
        ]);

        $response = $this->patchJson("/api/v1/customers/{$customer->id}", [
            'document_type' => null,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('document');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'document_type' => 'CC',
            'document_number' => '123456789',
        ]);
    }
}
