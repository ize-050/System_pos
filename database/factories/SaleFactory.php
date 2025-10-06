<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition()
    {
        $subtotal = $this->faker->randomFloat(2, 100, 10000);
        $discount = $this->faker->randomFloat(2, 0, $subtotal * 0.1);
        $taxAmount = ($subtotal - $discount) * 0.07;
        $totalAmount = $subtotal - $discount + $taxAmount;

        return [
            'sale_number' => 'SALE-' . $this->faker->unique()->numberBetween(10000, 99999),
            'sale_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'customer_id' => Customer::factory(),
            'cashier_id' => User::factory(),
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'payment_method' => $this->faker->randomElement(['cash', 'card', 'transfer', 'credit']),
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
