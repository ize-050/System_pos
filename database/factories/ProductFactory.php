<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'sku' => 'PRD-' . $this->faker->unique()->numberBetween(10000, 99999),
            'name' => $this->faker->words(3, true),
            'category_id' => ProductCategory::factory(),
            'description' => $this->faker->optional()->sentence(),
            'unit' => $this->faker->randomElement(['pcs', 'box', 'kg', 'liter']),
            'cost_price' => $this->faker->randomFloat(2, 10, 500),
            'selling_price' => $this->faker->randomFloat(2, 20, 1000),
            'current_stock' => $this->faker->numberBetween(0, 1000),
            'reorder_point' => $this->faker->numberBetween(10, 100),
            'is_active' => true,
        ];
    }
}
