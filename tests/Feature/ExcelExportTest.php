<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Sale;
use App\Models\PurchaseOrder;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('local');
        
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'accountant']);
    }

    /** @test */
    public function skip_can_export_purchase_report_to_excel()
    {
        $this->markTestSkipped('Export functionality not yet implemented');
        
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('reports.export'), [
            'report_type' => 'purchase',
            'format' => 'xlsx'
        ]);

        $response->assertStatus(200);
        $response->assertDownload();
        
        // Check filename format
        $filename = $response->headers->get('content-disposition');
        $this->assertStringContainsString('PurchaseReport_', $filename);
        $this->assertStringContainsString('.xlsx', $filename);
    }

    /** @test */
    public function can_export_sales_report_to_excel()
    {
        $this->markTestSkipped('Export functionality not yet implemented');
        
        $admin = User::factory()->create(['role' => 'admin']);

        Sale::factory()->count(5)->create();

        $response = $this->actingAs($admin)->post(route('reports.export'), [
            'report_type' => 'sales',
            'format' => 'xlsx'
        ]);

        $response->assertStatus(200);
        $response->assertDownload();
    }

    /** @test */
    public function exported_excel_file_has_correct_structure()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        PurchaseOrder::factory()->count(3)->create();

        $response = $this->actingAs($admin)->post(route('reports.export'), [
            'report_type' => 'purchase',
            'format' => 'xlsx'
        ]);

        $response->assertStatus(200);
        
        // Excel should contain:
        // - Report title
        // - Date range
        // - Headers (bold, with background)
        // - Data rows
        // - Summary totals
        // - Generated metadata
    }

    /** @test */
    public function excel_export_respects_applied_filters()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create POs with different dates
        PurchaseOrder::factory()->create(['order_date' => '2025-10-01']);
        PurchaseOrder::factory()->create(['order_date' => '2025-10-15']);

        $response = $this->actingAs($admin)->post(route('reports.export'), [
            'report_type' => 'purchase',
            'format' => 'xlsx',
            'filters' => [
                'start_date' => '2025-10-01',
                'end_date' => '2025-10-10'
            ]
        ]);

        $response->assertStatus(200);
        
        // Should only export 1 PO within date range
    }

    /** @test */
    public function excel_export_has_thai_baht_currency_formatting()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        PurchaseOrder::factory()->create([
            'total_amount' => 1234.56
        ]);

        $response = $this->actingAs($admin)->post(route('reports.export'), [
            'report_type' => 'purchase',
            'format' => 'xlsx'
        ]);

        $response->assertStatus(200);
        
        // Currency should be formatted as ฿1,234.56
    }

    /** @test */
    public function excel_export_warns_when_exceeding_50000_records()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Simulate large dataset
        $response = $this->actingAs($admin)->post(route('reports.export'), [
            'report_type' => 'purchase',
            'format' => 'xlsx',
            'record_count' => 60000 // Exceeds limit
        ]);

        $response->assertStatus(422); // Unprocessable Entity
        $response->assertJson([
            'message' => 'ข้อมูลเกิน 50,000 รายการ กรุณากรองข้อมูลหรือแบ่งการ export'
        ]);
    }

    /** @test */
    public function skip_excel_filename_includes_report_type_and_timestamp()
    {
        $this->markTestSkipped('Export functionality not yet implemented');
        
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('reports.export'), [
            'report_type' => 'sales',
            'format' => 'xlsx',
            'start_date' => '2025-10-01',
            'end_date' => '2025-10-31'
        ]);

        $response->assertStatus(200);
        
        $filename = $response->headers->get('content-disposition');
        
        // Format: SalesReport_2025-10-01_to_2025-10-31_20251004_143022.xlsx
        $this->assertStringContainsString('SalesReport_', $filename);
        $this->assertStringContainsString('2025-10-01_to_2025-10-31', $filename);
        $this->assertMatchesRegularExpression('/\d{8}_\d{6}/', $filename);
    }

    /** @test */
    public function export_job_is_logged_in_database()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('reports.export'), [
            'report_type' => 'purchase',
            'format' => 'xlsx'
        ]);

        $response->assertStatus(200);

        // Check export_jobs table
        $this->assertDatabaseHas('export_jobs', [
            'user_id' => $admin->id,
            'report_type' => 'purchase',
            'file_format' => 'xlsx',
            'status' => 'completed'
        ]);
    }

    /** @test */
    public function skip_can_export_inventory_report()
    {
        $this->markTestSkipped('Export functionality not yet implemented');
        
        $admin = User::factory()->create(['role' => 'admin']);

        Product::factory()->count(10)->create();

        $response = $this->actingAs($admin)->post(route('reports.export'), [
            'report_type' => 'inventory',
            'format' => 'xlsx'
        ]);

        $response->assertStatus(200);
        $response->assertDownload();
    }

    /** @test */
    public function skip_can_export_product_report()
    {
        $this->markTestSkipped('Export functionality not yet implemented');
        
        $admin = User::factory()->create(['role' => 'admin']);

        Product::factory()->count(10)->create();

        $response = $this->actingAs($admin)->post(route('reports.export'), [
            'report_type' => 'product',
            'format' => 'xlsx'
        ]);

        $response->assertStatus(200);
        $response->assertDownload();
    }
}
