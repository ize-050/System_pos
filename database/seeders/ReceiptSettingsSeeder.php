<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReceiptSettings;

class ReceiptSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ReceiptSettings::create([
            'shop_name' => 'ร้านค้า POS',
            'shop_address' => '123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110',
            'shop_phone' => '02-123-4567',
            'shop_email' => 'contact@posshop.com',
            'shop_website' => 'https://posshop.com',
            'shop_facebook' => 'POSShopOfficial',
            'shop_line_id' => '@posshop',
            'tax_id' => '0123456789012',
            'logo_path' => null,
            'promptpay_number' => '0812345678',
            'promptpay_name' => 'ร้านค้า POS',
            'show_logo' => true,
            'show_customer_info' => true,
            'show_vat' => true,
            'show_qr_code' => true,
            'show_barcode' => true,
            'show_notes' => true,
            'receipt_notes' => 'ขอบคุณที่ใช้บริการ กรุณาตรวจสอบสินค้าก่อนออกจากร้าน',
            'printer_type' => 'a4',
        ]);
    }
}
