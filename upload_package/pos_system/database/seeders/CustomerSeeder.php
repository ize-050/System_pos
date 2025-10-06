<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = [
            [
                'customer_code' => 'CUST001',
                'name' => 'สมชาย ใจดี',
                'email' => 'somchai@example.com',
                'phone' => '081-234-5678',
                'address' => '123 ถนนสุขุมวิท แขวงคลองตัน เขตวัฒนา กรุงเทพฯ 10110',
                'customer_type' => 'individual',
                'is_active' => true,
                'notes' => 'ลูกค้าประจำ ซื้อสินค้าเป็นประจำทุกเดือน',
                'created_by' => 1
            ],
            [
                'customer_code' => 'CUST002',
                'name' => 'สมหญิง รักดี',
                'email' => 'somying@example.com',
                'phone' => '082-345-6789',
                'address' => '456 ถนนพหลโยธิน แขวงลาดยาว เขตจตุจักร กรุงเทพฯ 10900',
                'customer_type' => 'individual',
                'is_active' => true,
                'notes' => 'ลูกค้าใหม่ สนใจสินค้าคุณภาพดี',
                'created_by' => 1
            ],
            [
                'customer_code' => 'CUST003',
                'name' => 'บริษัท เทคโนโลยี จำกัด',
                'email' => 'contact@technology.com',
                'phone' => '083-456-7890',
                'address' => '789 ถนนรัชดาภิเษก แขวงห้วยขวาง เขตห้วยขวาง กรุงเทพฯ 10310',
                'company_name' => 'บริษัท เทคโนโลยี จำกัด',
                'tax_id' => '0123456789012',
                'customer_type' => 'company',
                'credit_limit' => 50000.00,
                'payment_terms' => 30,
                'is_active' => true,
                'notes' => 'ลูกค้าองค์กร มีการสั่งซื้อเป็นจำนวนมาก',
                'created_by' => 1
            ],
            [
                'customer_code' => 'CUST004',
                'name' => 'สมศักดิ์ มั่งมี',
                'email' => 'somsak@example.com',
                'phone' => '084-567-8901',
                'address' => '321 ถนนเพชรบุรี แขวงมักกะสัน เขตราชเทวี กรุงเทพฯ 10400',
                'customer_type' => 'individual',
                'is_active' => true,
                'notes' => 'ลูกค้าเก่า มีความสัมพันธ์ที่ดี',
                'created_by' => 1
            ],
            [
                'customer_code' => 'CUST005',
                'name' => 'สมปอง สุขใจ',
                'email' => 'sompong@example.com',
                'phone' => '085-678-9012',
                'address' => '654 ถนนลาดพร้าว แขวงจอมพล เขตจตุจักร กรุงเทพฯ 10900',
                'customer_type' => 'individual',
                'is_active' => false,
                'notes' => 'ลูกค้าไม่ใช้งาน ย้ายที่อยู่',
                'created_by' => 1
            ]
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
