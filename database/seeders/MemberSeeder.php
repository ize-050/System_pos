<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ดึง admin user สำหรับ created_by
        $adminUser = User::where('role', 'admin')->first();
        
        if (!$adminUser) {
            $this->command->error('ไม่พบ admin user กรุณาสร้าง admin user ก่อน');
            return;
        }

        // ข้อมูลสมาชิกตัวอย่าง
        $members = [
            [
                'customer_code' => 'MEM001',
                'name' => 'สมชาย ใจดี',
                'phone' => '081-234-5678',
                'line_id' => 'somchai_jaidee',
                'email' => 'somchai@email.com',
                'address' => '123 ถนนสุขุมวิท แขวงคลองตัน เขตคลองตัน กรุงเทพฯ 10110',
                'customer_type' => 'individual',
                'credit_limit' => 50000.00,
                'current_balance' => 0.00,
                'payment_terms' => 30,
                'is_active' => true,
                'notes' => 'สมาชิกประจำ ซื้อวัสดุก่อสร้างเป็นประจำ',
                'created_by' => $adminUser->id,
            ],
            [
                'customer_code' => 'MEM002',
                'name' => 'สมหญิง รักดี',
                'phone' => '082-345-6789',
                'line_id' => 'somying_rakdee',
                'email' => 'somying@email.com',
                'address' => '456 ถนนพระราม 4 แขวงคลองตัน เขตคลองตัน กรุงเทพฯ 10110',
                'customer_type' => 'individual',
                'credit_limit' => 30000.00,
                'current_balance' => 0.00,
                'payment_terms' => 15,
                'is_active' => true,
                'notes' => 'สมาชิกใหม่ ซื้อเครื่องมือช่าง',
                'created_by' => $adminUser->id,
            ],
            [
                'customer_code' => 'MEM003',
                'name' => 'วิชัย สร้างบ้าน',
                'phone' => '083-456-7890',
                'line_id' => 'wichai_builder',
                'email' => 'wichai@email.com',
                'address' => '789 ถนนลาดพร้าว แขวงจตุจักร เขตจตุจักร กรุงเทพฯ 10900',
                'company_name' => 'บริษัท วิชัยก่อสร้าง จำกัด',
                'tax_id' => '0105558001234',
                'customer_type' => 'contractor',
                'credit_limit' => 200000.00,
                'current_balance' => 0.00,
                'payment_terms' => 45,
                'is_active' => true,
                'notes' => 'ผู้รับเหมาก่อสร้าง ซื้อวัสดุปริมาณมาก',
                'created_by' => $adminUser->id,
            ],
            [
                'customer_code' => 'MEM004',
                'name' => 'สุดา แต่งบ้าน',
                'phone' => '084-567-8901',
                'line_id' => 'suda_decor',
                'email' => 'suda@email.com',
                'address' => '321 ถนนรัชดาภิเษก แขวงห้วยขวง เขตห้วยขวง กรุงเทพฯ 10310',
                'customer_type' => 'individual',
                'credit_limit' => 25000.00,
                'current_balance' => 0.00,
                'payment_terms' => 30,
                'is_active' => true,
                'notes' => 'ซื้อวัสดุตแต่งบ้าน อุปกรณ์ตกแต่ง',
                'created_by' => $adminUser->id,
            ],
            [
                'customer_code' => 'MEM005',
                'name' => 'ประยุทธ ช่างไฟ',
                'phone' => '085-678-9012',
                'line_id' => 'prayuth_electric',
                'email' => 'prayuth@email.com',
                'address' => '654 ถนนพหลโยธิน แขวงสามเสนใน เขตพญาไท กรุงเทพฯ 10400',
                'company_name' => 'ร้านช่างไฟประยุทธ',
                'customer_type' => 'company',
                'credit_limit' => 75000.00,
                'current_balance' => 0.00,
                'payment_terms' => 30,
                'is_active' => true,
                'notes' => 'ช่างไฟฟ้า ซื้ออุปกรณ์ไฟฟ้าเป็นประจำ',
                'created_by' => $adminUser->id,
            ],
            [
                'customer_code' => 'MEM006',
                'name' => 'มานะ ช่างปูน',
                'phone' => '086-789-0123',
                'line_id' => 'mana_cement',
                'email' => 'mana@email.com',
                'address' => '987 ถนนบางนา แขวงบางนา เขตบางนา กรุงเทพฯ 10260',
                'customer_type' => 'individual',
                'credit_limit' => 40000.00,
                'current_balance' => 0.00,
                'payment_terms' => 30,
                'is_active' => true,
                'notes' => 'ช่างปูน ซื้อปูนซีเมนต์และวัสดุก่อสร้าง',
                'created_by' => $adminUser->id,
            ],
            [
                'customer_code' => 'MEM007',
                'name' => 'สมพร ช่างไม้',
                'phone' => '087-890-1234',
                'line_id' => 'somporn_wood',
                'email' => 'somporn@email.com',
                'address' => '147 ถนนเพชรบุรี แขวงมักกะสัน เขตราชเทวี กรุงเทพฯ 10400',
                'company_name' => 'ช่างไม้สมพร',
                'customer_type' => 'company',
                'credit_limit' => 60000.00,
                'current_balance' => 0.00,
                'payment_terms' => 30,
                'is_active' => true,
                'notes' => 'ช่างไม้ ซื้อไม้และอุปกรณ์ช่างไม้',
                'created_by' => $adminUser->id,
            ],
            [
                'customer_code' => 'MEM008',
                'name' => 'วิไล แม่บ้าน',
                'phone' => '088-901-2345',
                'line_id' => 'wilai_home',
                'email' => 'wilai@email.com',
                'address' => '258 ถนนสาทร แขวงสีลม เขตบางรัก กรุงเทพฯ 10500',
                'customer_type' => 'individual',
                'credit_limit' => 15000.00,
                'current_balance' => 0.00,
                'payment_terms' => 15,
                'is_active' => true,
                'notes' => 'ซื้อของใช้ในบ้าน อุปกรณ์ทำความสะอาด',
                'created_by' => $adminUser->id,
            ]
        ];

        // เพิ่มข้อมูลสมาชิก
        foreach ($members as $member) {
            Customer::create($member);
        }

        $this->command->info('สร้างข้อมูลสมาชิก ' . count($members) . ' คน เรียบร้อยแล้ว');
    }
}
