<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Customer;
use Carbon\Carbon;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get existing data
        $products = Product::all();
        $users = User::all();
        $customers = Customer::all();

        if ($products->isEmpty() || $users->isEmpty()) {
            $this->command->error('Please run ProductSeeder and UserSeeder first!');
            return;
        }

        // Create 10 sales orders
        for ($i = 1; $i <= 10; $i++) {
            $saleDate = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            
            // Random customer (can be null for walk-in customers)
            $customerId = $customers->isNotEmpty() && rand(0, 1) ? $customers->random()->id : null;
            
            // Random cashier
            $cashierId = $users->random()->id;
            
            // Random payment method
            $paymentMethods = ['cash', 'card', 'transfer', 'credit'];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            
            // Create sale
            $sale = Sale::create([
                'sale_number' => 'SALE-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'customer_id' => $customerId,
                'cashier_id' => $cashierId,
                'sale_date' => $saleDate,
                'subtotal' => 0, // Will be calculated
                'tax_amount' => 0, // Will be calculated
                'discount_amount' => rand(0, 1) ? rand(10, 100) : 0,
                'total_amount' => 0, // Will be calculated
                'payment_method' => $paymentMethod,
                'status' => 'completed',
                'notes' => rand(0, 1) ? 'Sample sale order #' . $i : null,
                'received_amount' => 0, // Will be calculated
                'change_amount' => 0, // Will be calculated
                'promotion_id' => null,
            ]);

            // Add 2-5 random items to each sale
            $itemCount = rand(2, 5);
            $subtotal = 0;
            
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $unitPrice = $product->selling_price;
                $totalPrice = $quantity * $unitPrice;
                $itemDiscount = rand(0, 1) ? rand(5, 20) : 0;
                
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'discount_amount' => $itemDiscount,
                ]);
                
                $subtotal += ($totalPrice - $itemDiscount);
            }
            
            // Calculate tax (7% VAT)
            $taxAmount = $subtotal * 0.07;
            $totalAmount = $subtotal + $taxAmount - $sale->discount_amount;
            
            // Calculate received amount and change for cash payments
            $receivedAmount = $totalAmount;
            $changeAmount = 0;
            
            if ($paymentMethod === 'cash') {
                // Round up to nearest 5 or 10 for realistic cash payments
                $receivedAmount = ceil($totalAmount / 10) * 10;
                $changeAmount = $receivedAmount - $totalAmount;
            }
            
            // Update sale totals
            $sale->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'received_amount' => $receivedAmount,
                'change_amount' => $changeAmount,
            ]);
            
            $this->command->info("Created sale: {$sale->sale_number} - Total: ฿" . number_format($totalAmount, 2));
        }
        
        $this->command->info('Sales seeder completed successfully!');
    }
}