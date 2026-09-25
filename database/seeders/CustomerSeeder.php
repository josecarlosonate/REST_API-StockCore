<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::factory()->count(7)->create();

        Customer::factory()->count(3)->create([
            'document_type' => null,
            'document_number' => null,
            'email' => null,
            'address' => null,
        ]);
    }
}
