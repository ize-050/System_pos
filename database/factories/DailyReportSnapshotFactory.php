<?php

namespace Database\Factories;

use App\Models\DailyReportSnapshot;
use Illuminate\Database\Eloquent\Factories\Factory;

class DailyReportSnapshotFactory extends Factory
{
    protected $model = DailyReportSnapshot::class;

    public function definition()
    {
        return [
            'report_date' => $this->faker->date(),
            'sales_total' => $this->faker->randomFloat(2, 1000, 50000),
            'purchase_total' => $this->faker->randomFloat(2, 500, 30000),
            'profit_loss' => $this->faker->randomFloat(2, -5000, 20000),
            'transaction_count' => $this->faker->numberBetween(15, 150),
            'generated_at' => now(),
        ];
    }
}
