<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Models\Supplier;

class PurchaseOrderModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_transition_to_valid_status()
    {
        $po = new PurchaseOrder(['status' => 'draft']);
        
        $this->assertTrue($po->canTransitionTo('pending'));
        $this->assertTrue($po->canTransitionTo('cancelled'));
        $this->assertFalse($po->canTransitionTo('received'));
        $this->assertFalse($po->canTransitionTo('shipping'));
    }

    public function test_generates_unique_po_number()
    {
        $poNumber1 = PurchaseOrder::generatePONumber();
        $poNumber2 = PurchaseOrder::generatePONumber();
        
        $this->assertStringStartsWith('PO-' . date('Y'), $poNumber1);
        $this->assertStringStartsWith('PO-' . date('Y'), $poNumber2);
    }

    public function test_has_supplier_relationship()
    {
        $supplier = Supplier::factory()->create();
        $po = PurchaseOrder::factory()->create(['supplier_id' => $supplier->id]);
        
        $this->assertInstanceOf(Supplier::class, $po->supplier);
        $this->assertEquals($supplier->id, $po->supplier->id);
    }

    public function test_has_created_by_relationship()
    {
        $user = User::factory()->create([
            'username' => 'testuser2',
            'email' => 'user2@test.com',
            'first_name' => 'Test',
            'last_name' => 'User2',
        ]);
        $po = PurchaseOrder::factory()->create(['created_by' => $user->id]);
        
        $this->assertInstanceOf(User::class, $po->createdBy);
        $this->assertEquals($user->id, $po->createdBy->id);
    }

    public function test_casts_dates_correctly()
    {
        $po = PurchaseOrder::factory()->create([
            'order_date' => '2025-01-01',
            'expected_delivery_date' => '2025-01-10',
        ]);
        
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $po->order_date);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $po->expected_delivery_date);
    }
}
