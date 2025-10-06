<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCategory;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ดึงหมวดหมู่ที่มีอยู่
        $categories = ProductCategory::all();
        
        // หา category IDs
        $toolsElectricId = $categories->where('name', 'เครื่องมือไฟฟ้า')->first()->id ?? null;
        $toolsHandId = $categories->where('name', 'เครื่องมือมือ')->first()->id ?? null;
        $cementId = $categories->where('name', 'ปูนซีเมนต์')->first()->id ?? null;
        $brickId = $categories->where('name', 'อิฐและบล็อก')->first()->id ?? null;
        $steelId = $categories->where('name', 'เหล็กเส้น')->first()->id ?? null;
        $electricianId = $categories->where('name', 'เครื่องมือช่างไฟ')->first()->id ?? null;
        $electricalId = $categories->where('name', 'อุปกรณ์ไฟฟ้า')->first()->id ?? null;
        $pipeId = $categories->where('name', 'ท่อและข้อต่อ')->first()->id ?? null;
        
        $products = [
            // อุปกรณ์ก่อสร้าง - เครื่องมือไฟฟ้า
            [
                'sku' => 'DRILL001',
                'barcode' => '1234567890001',
                'name' => 'สว่านไฟฟ้า BOSCH GSB 13 RE',
                'description' => 'สว่านไฟฟ้า 13 มม. 600 วัตต์ พร้อมกล่องเครื่องมือ',
                'category_id' => $toolsElectricId,
                'unit' => 'เครื่อง',
                'brand' => 'BOSCH',
                'model' => 'GSB 13 RE',
                'cost_price' => 2500.00,
                'selling_price' => 3200.00,
                'current_stock' => 15,
                'reorder_point' => 5,
                'is_active' => true,
            ],
            [
                'sku' => 'SAW001',
                'barcode' => '1234567890002',
                'name' => 'เลื่อยวงเดือน MAKITA 5704R',
                'description' => 'เลื่อยวงเดือน 7-1/4 นิ้ว 1200 วัตต์',
                'category_id' => $toolsElectricId,
                'unit' => 'เครื่อง',
                'brand' => 'MAKITA',
                'model' => '5704R',
                'cost_price' => 4500.00,
                'selling_price' => 5800.00,
                'current_stock' => 8,
                'reorder_point' => 3,
                'is_active' => true,
            ],
            
            // อุปกรณ์ก่อสร้าง - เครื่องมือมือ
            [
                'sku' => 'HAMMER001',
                'barcode' => '1234567890003',
                'name' => 'ค้อนหัวเหล็ก 16 oz',
                'description' => 'ค้อนหัวเหล็ก น้ำหนัก 16 ออนซ์ ด้ามไฟเบอร์กลาส',
                'category_id' => $toolsHandId,
                'unit' => 'อัน',
                'brand' => 'STANLEY',
                'cost_price' => 350.00,
                'selling_price' => 450.00,
                'current_stock' => 25,
                'reorder_point' => 10,
                'is_active' => true,
            ],
            [
                'sku' => 'SCREW001',
                'barcode' => '1234567890004',
                'name' => 'ไขควงชุด 6 ชิ้น',
                'description' => 'ไขควงแบน และ แฉก ขนาดต่างๆ 6 ชิ้น',
                'category_id' => $toolsHandId,
                'unit' => 'ชุด',
                'brand' => 'STANLEY',
                'cost_price' => 280.00,
                'selling_price' => 380.00,
                'current_stock' => 30,
                'reorder_point' => 15,
                'is_active' => true,
            ],
            
            // วัสดุก่อสร้าง - ปูนซีเมนต์
            [
                'sku' => 'CEMENT001',
                'barcode' => '1234567890005',
                'name' => 'ปูนซีเมนต์ตราช้าง',
                'description' => 'ปูนซีเมนต์ปอร์ตแลนด์ ตราช้าง ถุง 50 กก.',
                'category_id' => $cementId,
                'unit' => 'ถุง',
                'brand' => 'ตราช้าง',
                'weight' => 50.00,
                'cost_price' => 180.00,
                'selling_price' => 220.00,
                'current_stock' => 100,
                'reorder_point' => 20,
                'is_active' => true,
            ],
            [
                'sku' => 'CEMENT002',
                'barcode' => '1234567890006',
                'name' => 'ปูนซีเมนต์ TPI',
                'description' => 'ปูนซีเมนต์ปอร์ตแลนด์ TPI ถุง 50 กก.',
                'category_id' => $cementId,
                'unit' => 'ถุง',
                'brand' => 'TPI',
                'weight' => 50.00,
                'cost_price' => 175.00,
                'selling_price' => 215.00,
                'current_stock' => 80,
                'reorder_point' => 20,
                'is_active' => true,
            ],
            
            // วัสดุก่อสร้าง - อิฐและบล็อก
            [
                'sku' => 'BRICK001',
                'barcode' => '1234567890007',
                'name' => 'อิฐแดงมาตรฐาน',
                'description' => 'อิฐแดงมาตรฐาน ขนาด 6x11x22 ซม.',
                'category_id' => $brickId,
                'unit' => 'ก้อน',
                'dimensions' => '6x11x22 ซม.',
                'cost_price' => 3.50,
                'selling_price' => 4.50,
                'current_stock' => 500,
                'reorder_point' => 100,
                'is_active' => true,
            ],
            [
                'sku' => 'BLOCK001',
                'barcode' => '1234567890008',
                'name' => 'บล็อกมวลเบา 7.5 ซม.',
                'description' => 'บล็อกมวลเบา หนา 7.5 ซม. ขนาด 60x20x7.5 ซม.',
                'category_id' => $brickId,
                'unit' => 'ก้อน',
                'dimensions' => '60x20x7.5 ซม.',
                'cost_price' => 28.00,
                'selling_price' => 35.00,
                'current_stock' => 200,
                'reorder_point' => 50,
                'is_active' => true,
            ],
            
            // วัสดุก่อสร้าง - เหล็กเส้น
            [
                'sku' => 'STEEL001',
                'barcode' => '1234567890009',
                'name' => 'เหล็กเส้น RB6 ยาว 12 ม.',
                'description' => 'เหล็กเส้นข้ออ้อย RB6 ยาว 12 เมตร',
                'category_id' => $steelId,
                'unit' => 'เส้น',
                'dimensions' => 'เส้นผ่านศูนย์กลาง 6 มม. ยาว 12 ม.',
                'cost_price' => 45.00,
                'selling_price' => 55.00,
                'current_stock' => 150,
                'reorder_point' => 30,
                'is_active' => true,
            ],
            [
                'sku' => 'STEEL002',
                'barcode' => '1234567890010',
                'name' => 'เหล็กเส้น RB9 ยาว 12 ม.',
                'description' => 'เหล็กเส้นข้ออ้อย RB9 ยาว 12 เมตร',
                'category_id' => $steelId,
                'unit' => 'เส้น',
                'dimensions' => 'เส้นผ่านศูนย์กลาง 9 มม. ยาว 12 ม.',
                'cost_price' => 85.00,
                'selling_price' => 105.00,
                'current_stock' => 120,
                'reorder_point' => 25,
                'is_active' => true,
            ],
            
            // เครื่องมือช่าง - เครื่องมือช่างไฟ
            [
                'sku' => 'ELEC001',
                'barcode' => '1234567890011',
                'name' => 'คีมปากจิ้งจก 8 นิ้ว',
                'description' => 'คีมปากจิ้งจก ขนาด 8 นิ้ว ฉนวนไฟฟ้า',
                'category_id' => $electricianId,
                'unit' => 'อัน',
                'size' => '8 นิ้ว',
                'cost_price' => 180.00,
                'selling_price' => 250.00,
                'current_stock' => 40,
                'reorder_point' => 15,
                'is_active' => true,
            ],
            [
                'sku' => 'ELEC002',
                'barcode' => '1234567890012',
                'name' => 'ที่ลอกสายไฟ',
                'description' => 'ที่ลอกสายไฟอัตโนมัติ ขนาด 0.2-6.0 มม²',
                'category_id' => $electricianId,
                'unit' => 'อัน',
                'specifications' => 'ขนาดสาย 0.2-6.0 มม²',
                'cost_price' => 320.00,
                'selling_price' => 420.00,
                'current_stock' => 25,
                'reorder_point' => 10,
                'is_active' => true,
            ],
            
            // อุปกรณ์ไฟฟ้า
            [
                'sku' => 'WIRE001',
                'barcode' => '1234567890013',
                'name' => 'สายไฟ THW 2.5 มม² สีแดง',
                'description' => 'สายไฟ THW ขนาด 2.5 มม² สีแดง ยาว 100 เมตร',
                'category_id' => $electricalId,
                'unit' => 'เมตร',
                'color' => 'แดง',
                'specifications' => 'THW 2.5 มม²',
                'cost_price' => 12.00,
                'selling_price' => 15.00,
                'current_stock' => 1000,
                'reorder_point' => 200,
                'is_active' => true,
            ],
            [
                'sku' => 'SWITCH001',
                'barcode' => '1234567890014',
                'name' => 'สวิตช์ 1 ทาง PANASONIC',
                'description' => 'สวิตช์ 1 ทาง 15A 250V สีขาว',
                'category_id' => $electricalId,
                'unit' => 'อัน',
                'brand' => 'PANASONIC',
                'color' => 'ขาว',
                'specifications' => '15A 250V',
                'cost_price' => 45.00,
                'selling_price' => 65.00,
                'current_stock' => 100,
                'reorder_point' => 25,
                'is_active' => true,
            ],
            
            // ท่อและข้อต่อ
            [
                'sku' => 'PIPE001',
                'barcode' => '1234567890015',
                'name' => 'ท่อ PVC 4 นิ้ว ยาว 4 ม.',
                'description' => 'ท่อ PVC ขาว ขนาด 4 นิ้ว ยาว 4 เมตร',
                'category_id' => $pipeId,
                'unit' => 'เส้น',
                'size' => '4 นิ้ว',
                'dimensions' => 'ยาว 4 เมตร',
                'material' => 'PVC',
                'color' => 'ขาว',
                'cost_price' => 180.00,
                'selling_price' => 230.00,
                'current_stock' => 60,
                'reorder_point' => 15,
                'is_active' => true,
            ],
            [
                'sku' => 'JOINT001',
                'barcode' => '1234567890016',
                'name' => 'ข้อต่อ T PVC 4 นิ้ว',
                'description' => 'ข้อต่อ T PVC ขนาด 4 นิ้ว',
                'category_id' => $pipeId,
                'unit' => 'อัน',
                'size' => '4 นิ้ว',
                'material' => 'PVC',
                'cost_price' => 85.00,
                'selling_price' => 110.00,
                'current_stock' => 80,
                'reorder_point' => 20,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}