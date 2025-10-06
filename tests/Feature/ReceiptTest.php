<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Sale;
use App\Models\ReceiptSettings;

class ReceiptTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $sale;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'username' => 'testuser',
            'email' => 'user@test.com',
            'first_name' => 'Test',
            'last_name' => 'User',
        ]);
        $this->sale = Sale::factory()->create();
        
        ReceiptSettings::create([
            'shop_name' => 'Test Shop',
            'show_logo' => true,
            'show_qr_code' => true,
            'show_barcode' => true,
            'printer_type' => 'a4',
        ]);
    }

    public function test_can_preview_receipt()
    {
        $response = $this->actingAs($this->user)
            ->get(route('receipts.preview', $this->sale->id));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Receipts/Preview')
            ->has('sale')
            ->has('settings')
        );
    }

    public function test_can_download_receipt_pdf()
    {
        $response = $this->actingAs($this->user)
            ->get(route('receipts.download-pdf', $this->sale->id));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_receipt_settings_singleton()
    {
        $settings1 = ReceiptSettings::getSettings();
        $settings2 = ReceiptSettings::getSettings();

        $this->assertEquals($settings1->id, $settings2->id);
        $this->assertEquals(1, ReceiptSettings::count());
    }

    public function test_can_update_receipt_settings()
    {
        $response = $this->actingAs($this->user)
            ->post(route('receipt-settings.update'), [
                'shop_name' => 'Updated Shop',
                'shop_address' => '123 Test St',
                'show_logo' => false,
                'show_vat' => true,
                'printer_type' => 'thermal_80mm',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('receipt_settings', [
            'shop_name' => 'Updated Shop',
            'printer_type' => 'thermal_80mm',
        ]);
    }
}
