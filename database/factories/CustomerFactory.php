<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'document_type' => fake()->randomElement(['CC', 'CE', 'NIT', 'PASSPORT']),
            'document_number' => fake()->unique()->numerify('##########'),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('3#########'),
            'address' => fake()->address(),
            'is_active' => true,
        ];
    }
}
