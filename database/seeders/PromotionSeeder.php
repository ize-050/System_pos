<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Promotion;
use Carbon\Carbon;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing promotions instead of truncate to avoid foreign key constraint issues
        Promotion::query()->delete();
        
        $promotions = [
            // โปรโมชั่นที่ผูกกับสินค้าเฉพาะ (สว่านไฟฟ้า BOSCH)
            [
                'name' => 'ลดพิเศษสว่านไฟฟ้า BOSCH',
                'description' => 'ส่วนลด 15% สำหรับสว่านไฟฟ้า BOSCH GSB 13 RE เท่านั้น',
                'type' => 'percentage',
                'value' => 15.00,
                'min_quantity' => 1,
                'min_amount' => null,
                'max_discount' => 500.00,
                'applicable_products' => json_encode([1]), // Product ID 1 (สว่านไฟฟ้า BOSCH)
                'applicable_categories' => json_encode([]),
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(1),
                'is_active' => true,
                'usage_limit' => 50,
                'usage_count' => 0
            ],
            
            // โปรโมชั่นทั่วไป ไม่ผูกกับสินค้าใดๆ (ซื้อ 500 ลด 10%)
            [
                'name' => 'ซื้อครบ 500 ลด 10%',
                'description' => 'ส่วนลด 10% เมื่อซื้อสินค้าครบ 500 บาท ใช้ได้กับทุกสินค้า',
                'type' => 'percentage',
                'value' => 10.00,
                'min_quantity' => null,
                'min_amount' => 500.00,
                'max_discount' => 200.00,
                'applicable_products' => json_encode([]), // ไม่ผูกกับสินค้าใดๆ
                'applicable_categories' => json_encode([]),
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(2),
                'is_active' => true,
                'usage_limit' => 1000,
                'usage_count' => 0
            ],
            
            // โปรโมชั่นเพิ่มเติมสำหรับทดสอบ
            [
                'name' => 'ลดราคาเลื่อยวงเดือน',
                'description' => 'ลดทันที 100 บาท สำหรับเลื่อยวงเดือน MAKITA',
                'type' => 'fixed_amount',
                'value' => 100.00,
                'min_quantity' => 1,
                'min_amount' => null,
                'max_discount' => null,
                'applicable_products' => json_encode([2]), // Product ID 2 (เลื่อยวงเดือน MAKITA)
                'applicable_categories' => json_encode([]),
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addWeeks(3),
                'is_active' => true,
                'usage_limit' => 30,
                'usage_count' => 0
            ]
        ];

        foreach ($promotions as $promotion) {
            Promotion::create($promotion);
        }
        
        echo "Created " . count($promotions) . " promotions successfully!\n";
    }
}
