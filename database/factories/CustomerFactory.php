<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition()
    {
        return [
            'customer_code' => 'CUST-' . $this->faker->unique()->numberBetween(10000, 99999),
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->optional()->safeEmail(),
            'address' => $this->faker->optional()->address(),
            'customer_type' => $this->faker->randomElement(['individual', 'company', 'contractor']),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }
}
