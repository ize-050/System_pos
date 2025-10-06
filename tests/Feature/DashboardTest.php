<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Sale;
use App\Models\PurchaseOrder;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'accountant']);
        Role::create(['name' => 'cashier']);
    }

    /** @test */
    public function admin_can_access_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard'));
    }

    /** @test */
    public function manager_can_access_dashboard()
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $response = $this->actingAs($manager)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function accountant_can_access_dashboard()
    {
        $accountant = User::factory()->create(['role' => 'accountant']);

        $response = $this->actingAs($accountant)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function cashier_cannot_access_dashboard_analytics()
    {
        $cashier = User::factory()->create(['role' => 'cashier']);

        $response = $this->actingAs($cashier)->get(route('dashboard'));

        // Cashier can access dashboard but not see analytics widgets
        $response->assertStatus(200);
        $this->assertFalse($response->viewData('showAnalytics'));
    }

    /** @test */
    public function dashboard_displays_todays_summary()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create today's sales
        Sale::factory()->count(5)->create([
            'sale_date' => now()->toDateString(),
            'total_amount' => 1000
        ]);

        // Create today's purchases
        PurchaseOrder::factory()->count(3)->create([
            'order_date' => now()->toDateString(),
            'total_amount' => 500,
            'status' => 'received'
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        
        $summary = $response->viewData('todaysSummary');
        $this->assertEquals(5000, $summary['sales_total']);
        $this->assertEquals(1500, $summary['purchase_total']);
        $this->assertEquals(5, $summary['transaction_count']);
    }

    /** @test */
    public function dashboard_shows_sales_vs_purchases_chart_data()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create data for last 30 days
        for ($i = 0; $i < 30; $i++) {
            $date = now()->subDays($i)->toDateString();
            
            Sale::factory()->create([
                'sale_date' => $date,
                'total_amount' => 1000
            ]);
            
            PurchaseOrder::factory()->create([
                'order_date' => $date,
                'total_amount' => 500,
                'status' => 'received'
            ]);
        }

        $response = $this->actingAs($admin)->get(route('dashboard', ['period' => 30]));

        $response->assertStatus(200);
        
        $chartData = $response->viewData('chartData');
        $this->assertCount(30, $chartData['dates']);
        $this->assertCount(30, $chartData['sales']);
        $this->assertCount(30, $chartData['purchases']);
    }

    /** @test */
    public function dashboard_shows_top_10_best_selling_products()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create 15 products with different sales volumes
        $products = Product::factory()->count(15)->create();
        
        foreach ($products as $index => $product) {
            Sale::factory()->create([
                'product_id' => $product->id,
                'quantity' => 15 - $index // Descending order
            ]);
        }

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        
        $topProducts = $response->viewData('topProducts');
        $this->assertCount(10, $topProducts); // Only top 10
        $this->assertEquals($products[0]->id, $topProducts[0]['product_id']);
    }

    /** @test */
    public function dashboard_shows_low_stock_alerts()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create products with low stock
        Product::factory()->count(5)->create([
            'current_stock' => 5,
            'minimum_stock' => 10
        ]);

        // Create products with normal stock
        Product::factory()->count(5)->create([
            'current_stock' => 50,
            'minimum_stock' => 10
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        
        $lowStockProducts = $response->viewData('lowStockAlerts');
        $this->assertCount(5, $lowStockProducts); // Only low stock items
    }

    /** @test */
    public function dashboard_calculates_profit_loss_correctly()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create sales with known revenue
        Sale::factory()->count(10)->create([
            'total_amount' => 1000, // Total revenue: 10,000
            'cost_of_goods' => 600  // Total COGS: 6,000
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        
        $profitLoss = $response->viewData('profitLoss');
        $this->assertEquals(10000, $profitLoss['total_revenue']);
        $this->assertEquals(6000, $profitLoss['total_cogs']);
        $this->assertEquals(4000, $profitLoss['gross_profit']);
        $this->assertEquals(40, $profitLoss['profit_margin_percentage']);
    }

    /** @test */
    public function dashboard_loads_within_3_seconds_for_10000_records()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create 10,000 records
        Sale::factory()->count(5000)->create();
        PurchaseOrder::factory()->count(5000)->create();

        $startTime = microtime(true);
        
        $response = $this->actingAs($admin)->get(route('dashboard'));
        
        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(3, $loadTime, 'Dashboard should load within 3 seconds');
    }

    /** @test */
    public function dashboard_chart_period_can_be_changed()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Test 7 days period
        $response = $this->actingAs($admin)->get(route('dashboard', ['period' => 7]));
        $response->assertStatus(200);
        $chartData = $response->viewData('chartData');
        $this->assertCount(7, $chartData['dates']);

        // Test 30 days period
        $response = $this->actingAs($admin)->get(route('dashboard', ['period' => 30]));
        $response->assertStatus(200);
        $chartData = $response->viewData('chartData');
        $this->assertCount(30, $chartData['dates']);

        // Test 90 days period
        $response = $this->actingAs($admin)->get(route('dashboard', ['period' => 90]));
        $response->assertStatus(200);
        $chartData = $response->viewData('chartData');
        $this->assertCount(90, $chartData['dates']);
    }
}
