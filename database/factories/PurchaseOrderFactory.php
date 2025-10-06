<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'po_number' => PurchaseOrder::generatePONumber(),
            'supplier_id' => Supplier::factory(),
            'order_date' => $this->faker->date(),
            'expected_delivery_date' => $this->faker->date(),
            'subtotal' => $this->faker->randomFloat(2, 100, 10000),
            'discount_amount' => 0,
            'vat_amount' => function (array $attributes) {
                return ($attributes['subtotal'] - $attributes['discount_amount']) * 0.07;
            },
            'total_amount' => function (array $attributes) {
                return $attributes['subtotal'] - $attributes['discount_amount'] + $attributes['vat_amount'];
            },
            'status' => 'draft',
            'notes' => $this->faker->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
