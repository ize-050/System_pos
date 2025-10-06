<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suppliers = [
            [
                'name' => 'บริษัท เทคโนโลยี จำกัด',
                'contact_person' => 'คุณสมศักดิ์ วิทยากร',
                'email' => 'contact@technology.co.th',
                'phone' => '02-123-4567',
                'address' => '999 อาคารเทคโนโลยี ถนนพระราม 4 แขวงสุริยวงศ์ เขตบางรัก กรุงเทพฯ 10500',
                'tax_id' => '0105558000123',
                'is_active' => true,
                'notes' => 'ผู้จัดจำหน่ายอุปกรณ์เทคโนโลยี คุณภาพดี ส่งของตรงเวลา'
            ],
            [
                'name' => 'ห้างหุ้นส่วน แฟชั่นสไตล์',
                'contact_person' => 'คุณสุดา แฟชั่น',
                'email' => 'info@fashionstyle.com',
                'phone' => '02-234-5678',
                'address' => '555 ตลาดแฟชั่น ถนนสีลม แขวงสีลม เขตบางรัก กรุงเทพฯ 10500',
                'tax_id' => '0105558000456',
                'is_active' => true,
                'notes' => 'จำหน่ายเสื้อผ้าแฟชั่น ราคาดี มีสินค้าใหม่ทุกเดือน'
            ],
            [
                'name' => 'บริษัท อาหารสุขภาพ จำกัด',
                'contact_person' => 'คุณมานะ สุขภาพดี',
                'email' => 'sales@healthyfood.co.th',
                'phone' => '02-345-6789',
                'address' => '777 ซอยสุขภาพ ถนนเพชรบุรี แขวงมักกะสัน เขตราชเทวี กรุงเทพฯ 10400',
                'tax_id' => '0105558000789',
                'is_active' => true,
                'notes' => 'ผลิตภัณฑ์อาหารเสริม อาหารสุขภาพ มีใบรับรองคุณภาพ'
            ],
            [
                'name' => 'ร้านเครื่องใช้ไฟฟ้า โมเดิร์น',
                'contact_person' => 'คุณวิชัย ไฟฟ้า',
                'email' => 'modern@electric.com',
                'phone' => '02-456-7890',
                'address' => '888 ถนนรัชดาภิเษก แขวงห้วยขวง เขตห้วยขวง กรุงเทพฯ 10310',
                'tax_id' => '0105558001012',
                'is_active' => true,
                'notes' => 'เครื่องใช้ไฟฟ้าคุณภาพสูง มีการรับประกัน บริการหลังการขายดี'
            ],
            [
                'name' => 'บริษัท เฟอร์นิเจอร์ดีไซน์ จำกัด',
                'contact_person' => 'คุณสวย ดีไซน์',
                'email' => 'design@furniture.co.th',
                'phone' => '02-567-8901',
                'address' => '111 ถนนลาดพร้าว แขวงจอมพล เขตจตุจักร กรุงเทพฯ 10900',
                'tax_id' => '0105558001345',
                'is_active' => false,
                'notes' => 'เฟอร์นิเจอร์ออกแบบพิเศษ ปัจจุบันหยุดจำหน่าย'
            ]
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
