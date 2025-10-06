<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $supplier;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'username' => 'testadmin',
            'email' => 'admin@test.com',
            'first_name' => 'Test',
            'last_name' => 'Admin',
            'role' => 'admin',
        ]);
        $this->supplier = Supplier::factory()->create();
        $this->product = Product::factory()->create([
            'current_stock' => 10,
            'cost_price' => 100,
        ]);
    }

    public function test_can_create_purchase_order()
    {
        $response = $this->actingAs($this->user)->post(route('purchase-orders.store'), [
            'supplier_id' => $this->supplier->id,
            'order_date' => now()->format('Y-m-d'),
            'expected_delivery_date' => now()->addDays(7)->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_name' => $this->product->name,
                    'quantity_ordered' => 5,
                    'unit_price' => 100,
                ]
            ],
            'discount_amount' => 0,
            'notes' => 'Test PO',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', [
            'supplier_id' => $this->supplier->id,
            'status' => 'draft',
        ]);
    }

    public function test_po_number_is_generated_automatically()
    {
        $po = PurchaseOrder::create([
            'po_number' => PurchaseOrder::generatePONumber(),
            'supplier_id' => $this->supplier->id,
            'order_date' => now(),
            'subtotal' => 500,
            'vat_amount' => 35,
            'total_amount' => 535,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $this->assertStringStartsWith('PO-' . date('Y'), $po->po_number);
    }

    public function test_can_send_purchase_order()
    {
        $po = PurchaseOrder::factory()->create([
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('purchase-orders.send', $po->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $po->id,
            'status' => 'pending',
        ]);
    }

    public function test_can_receive_goods_and_update_stock()
    {
        $po = PurchaseOrder::factory()->create([
            'status' => 'pending',
            'supplier_id' => $this->supplier->id,
            'created_by' => $this->user->id,
        ]);

        $item = PurchaseOrderItem::create([
            'po_id' => $po->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity_ordered' => 10,
            'quantity_received' => 0,
            'unit_price' => 100,
            'total_price' => 1000,
        ]);

        $initialStock = $this->product->current_stock;

        $response = $this->actingAs($this->user)
            ->post(route('purchase-orders.receive.store', $po->id), [
                'received_date' => now()->format('Y-m-d'),
                'items' => [
                    [
                        'id' => $item->id,
                        'quantity_received' => 10,
                        'condition_status' => 'good',
                        'notes' => '',
                    ]
                ],
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $po->id,
            'status' => 'received',
        ]);

        $this->product->refresh();
        $this->assertEquals($initialStock + 10, $this->product->current_stock);
    }

    public function test_cannot_edit_non_draft_purchase_order()
    {
        $po = PurchaseOrder::factory()->create([
            'status' => 'received',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('purchase-orders.update', $po->id), [
                'supplier_id' => $this->supplier->id,
                'order_date' => now()->format('Y-m-d'),
                'items' => [],
            ]);

        $response->assertSessionHas('error');
    }

    public function test_can_cancel_purchase_order()
    {
        $po = PurchaseOrder::factory()->create([
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('purchase-orders.destroy', $po->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $po->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_status_transition_validation()
    {
        $po = new PurchaseOrder(['status' => 'draft']);
        
        $this->assertTrue($po->canTransitionTo('pending'));
        $this->assertTrue($po->canTransitionTo('cancelled'));
        $this->assertFalse($po->canTransitionTo('received'));
    }
}
