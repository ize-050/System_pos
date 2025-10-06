<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\User;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get or create suppliers
        $suppliers = Supplier::all();
        if ($suppliers->count() === 0) {
            $suppliers = Supplier::factory()->count(5)->create();
        }

        // Get or create products
        $products = Product::all();
        if ($products->count() === 0) {
            $products = Product::factory()->count(20)->create();
        }

        // Get admin user
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::factory()->create([
                'username' => 'admin',
                'email' => 'admin@pos.com',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'role' => 'admin',
            ]);
        }

        // Create Purchase Orders with different statuses
        $statuses = ['draft', 'pending', 'shipping', 'received', 'cancelled'];
        
        foreach ($statuses as $status) {
            for ($i = 0; $i < 3; $i++) {
                $supplier = $suppliers->random();
                
                // Create PO
                $po = PurchaseOrder::create([
                    'po_number' => PurchaseOrder::generatePONumber(),
                    'supplier_id' => $supplier->id,
                    'order_date' => now()->subDays(rand(1, 30)),
                    'expected_delivery_date' => now()->addDays(rand(1, 14)),
                    'received_date' => $status === 'received' ? now()->subDays(rand(1, 5)) : null,
                    'subtotal' => 0,
                    'discount_amount' => 0,
                    'vat_amount' => 0,
                    'total_amount' => 0,
                    'status' => $status,
                    'notes' => 'Sample PO for ' . $status . ' status',
                    'created_by' => $admin->id,
                    'received_by' => $status === 'received' ? $admin->id : null,
                ]);

                // Add items to PO
                $itemCount = rand(2, 5);
                $subtotal = 0;

                for ($j = 0; $j < $itemCount; $j++) {
                    $product = $products->random();
                    $quantity = rand(5, 50);
                    $unitPrice = $product->cost_price ?? rand(100, 1000);
                    $totalPrice = $quantity * $unitPrice;
                    $subtotal += $totalPrice;

                    PurchaseOrderItem::create([
                        'po_id' => $po->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity_ordered' => $quantity,
                        'quantity_received' => $status === 'received' ? $quantity : 0,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'condition_status' => $status === 'received' ? 'good' : 'good',
                        'notes' => null,
                    ]);
                }

                // Update PO totals
                $discountAmount = 0;
                $afterDiscount = $subtotal - $discountAmount;
                $vatAmount = $afterDiscount * 0.07;
                $totalAmount = $afterDiscount + $vatAmount;

                $po->update([
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'vat_amount' => $vatAmount,
                    'total_amount' => $totalAmount,
                ]);
            }
        }

        $this->command->info('Created ' . (count($statuses) * 3) . ' Purchase Orders with items');
    }
}
