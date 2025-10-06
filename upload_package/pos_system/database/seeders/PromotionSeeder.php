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
        $promotions = [
            [
                'name' => 'ลดราคา 10% สำหรับสมาชิก',
                'description' => 'ส่วนลดพิเศษ 10% สำหรับลูกค้าสมาชิก ใช้ได้กับทุกสินค้า',
                'type' => 'percentage',
                'value' => 10.00,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(3),
                'is_active' => true,
                'usage_limit' => 1000,
                'usage_count' => 0
            ],
            [
                'name' => 'ซื้อ 2 แถม 1',
                'description' => 'ซื้อสินค้า 2 ชิ้น แถมฟรี 1 ชิ้น สำหรับสินค้าที่ร่วมรายการ',
                'type' => 'buy_x_get_y',
                'value' => 2.00,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(2),
                'is_active' => true,
                'usage_limit' => 500,
                'usage_count' => 0
            ],
            [
                'name' => 'ลดทันที 50 บาท',
                'description' => 'ลดราคาทันที 50 บาท เมื่อซื้อครบ 500 บาท',
                'type' => 'fixed_amount',
                'value' => 50.00,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonth(),
                'is_active' => true,
                'usage_limit' => 200,
                'usage_count' => 0
            ],
            [
                'name' => 'Flash Sale 50%',
                'description' => 'ลดราคาพิเศษ 50% สำหรับสินค้าที่ร่วมรายการ (จำกัดเวลา)',
                'type' => 'percentage',
                'value' => 50.00,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(7),
                'is_active' => true,
                'usage_limit' => 100,
                'usage_count' => 0
            ],
            [
                'name' => 'โปรโมชั่นวันหยุด',
                'description' => 'ส่วนลดพิเศษ 20% สำหรับการซื้อในวันหยุดสุดสัปดาห์',
                'type' => 'percentage',
                'value' => 20.00,
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addDays(5),
                'is_active' => false,
                'usage_limit' => 100,
                'usage_count' => 45
            ],
            [
                'name' => 'ส่วนลดสินค้าหมวดเครื่องดื่ม',
                'description' => 'ลดราคา 15% สำหรับสินค้าในหมวดเครื่องดื่มทุกชนิด',
                'type' => 'percentage',
                'value' => 15.00,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addWeeks(2),
                'is_active' => true,
                'usage_limit' => 300,
                'usage_count' => 0
            ]
        ];

        foreach ($promotions as $promotion) {
            Promotion::create($promotion);
        }
    }
}
