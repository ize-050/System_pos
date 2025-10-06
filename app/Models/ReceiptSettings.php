<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_name',
        'shop_address',
        'shop_phone',
        'shop_email',
        'shop_website',
        'shop_facebook',
        'shop_line_id',
        'tax_id',
        'logo_path',
        'promptpay_number',
        'promptpay_name',
        'show_logo',
        'show_customer_info',
        'show_vat',
        'show_qr_code',
        'show_barcode',
        'show_notes',
        'receipt_notes',
        'printer_type',
    ];

    protected $casts = [
        'show_logo' => 'boolean',
        'show_customer_info' => 'boolean',
        'show_vat' => 'boolean',
        'show_qr_code' => 'boolean',
        'show_barcode' => 'boolean',
        'show_notes' => 'boolean',
    ];

    // Singleton pattern - only one settings record
    public static function getSettings()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'shop_name' => 'ร้านค้า',
                'receipt_notes' => 'กรุณาตรวจสอบสินค้าก่อนออกจากร้าน',
                'show_logo' => true,
                'show_customer_info' => true,
                'show_vat' => true,
                'show_qr_code' => true,
                'show_barcode' => true,
                'show_notes' => true,
                'printer_type' => 'a4',
            ]);
        }
        
        return $settings;
    }
}
