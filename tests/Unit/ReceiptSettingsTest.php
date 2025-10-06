<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ReceiptSettings;

class ReceiptSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_singleton_pattern_returns_same_instance()
    {
        $settings1 = ReceiptSettings::getSettings();
        $settings2 = ReceiptSettings::getSettings();
        
        $this->assertEquals($settings1->id, $settings2->id);
        $this->assertEquals(1, ReceiptSettings::count());
    }

    public function test_creates_default_settings_if_none_exist()
    {
        $this->assertEquals(0, ReceiptSettings::count());
        
        $settings = ReceiptSettings::getSettings();
        
        $this->assertNotNull($settings);
        $this->assertEquals('ร้านค้า', $settings->shop_name);
        $this->assertTrue($settings->show_logo);
        $this->assertTrue($settings->show_vat);
        $this->assertEquals('a4', $settings->printer_type);
    }

    public function test_boolean_fields_cast_correctly()
    {
        $settings = ReceiptSettings::create([
            'shop_name' => 'Test',
            'show_logo' => 1,
            'show_vat' => 0,
            'show_qr_code' => true,
            'printer_type' => 'a4',
        ]);
        
        $this->assertTrue($settings->show_logo);
        $this->assertFalse($settings->show_vat);
        $this->assertTrue($settings->show_qr_code);
        $this->assertIsBool($settings->show_logo);
        $this->assertIsBool($settings->show_vat);
    }
}
