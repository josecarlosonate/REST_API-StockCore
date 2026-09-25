<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Laravel\Sanctum\Sanctum;

abstract class ApiTestCase extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole('admin');

        Sanctum::actingAs($this->user);
    }
}
