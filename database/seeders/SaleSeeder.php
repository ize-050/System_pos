<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get existing data
        $customers = Customer::all();
        $products = Product::all();
        $cashiers = User::where('role', 'cashier')->get();
        
        if ($cashiers->isEmpty()) {
            $cashiers = User::all();
        }

        $sales = [
            [
                'sale_number' => 'SL-' . date('Ymd') . '-001',
                'customer_id' => $customers->first() ? $customers->first()->id : null,
                'cashier_id' => $cashiers->first() ? $cashiers->first()->id : null,
                'sale_date' => Carbon::now()->subDays(5),
                'subtotal' => 1500.00,
                'tax_amount' => 105.00,
                'discount_amount' => 0.00,
                'total_amount' => 1605.00,
                'status' => 'completed',
                'payment_method' => 'cash',
                'notes' => 'การขายปกติ ลูกค้าจ่ายเงินสด',
                'created_at' => Carbon::now()->subDays(5)
            ],
            [
                'sale_number' => 'SL-' . date('Ymd') . '-002',
                'customer_id' => $customers->skip(1)->first() ? $customers->skip(1)->first()->id : null,
                'cashier_id' => $cashiers->first() ? $cashiers->first()->id : null,
                'sale_date' => Carbon::now()->subDays(3),
                'subtotal' => 2800.00,
                'tax_amount' => 196.00,
                'discount_amount' => 100.00,
                'total_amount' => 2896.00,
                'status' => 'completed',
                'payment_method' => 'card',
                'notes' => 'ลูกค้าใช้บัตรเครดิต ได้รับส่วนลด',
                'created_at' => Carbon::now()->subDays(3)
            ],
            [
                'sale_number' => 'SL-' . date('Ymd') . '-003',
                'customer_id' => $customers->skip(2)->first() ? $customers->skip(2)->first()->id : null,
                'cashier_id' => $cashiers->skip(1)->first() ? $cashiers->skip(1)->first()->id : ($cashiers->first() ? $cashiers->first()->id : null),
                'sale_date' => Carbon::now()->subDays(2),
                'subtotal' => 950.00,
                'tax_amount' => 66.50,
                'discount_amount' => 50.00,
                'total_amount' => 966.50,
                'status' => 'completed',
                'payment_method' => 'transfer',
                'notes' => 'โอนเงินผ่านธนาคาร',
                'created_at' => Carbon::now()->subDays(2)
            ],
            [
                'sale_number' => 'SL-' . date('Ymd') . '-004',
                'customer_id' => null, // Walk-in customer
                'cashier_id' => $cashiers->first() ? $cashiers->first()->id : null,
                'sale_date' => Carbon::now()->subDays(1),
                'subtotal' => 750.00,
                'tax_amount' => 52.50,
                'discount_amount' => 0.00,
                'total_amount' => 802.50,
                'status' => 'completed',
                'payment_method' => 'cash',
                'notes' => 'ลูกค้าทั่วไป ไม่มีข้อมูลสมาชิก',
                'created_at' => Carbon::now()->subDays(1)
            ],
            [
                'sale_number' => 'SL-' . date('Ymd') . '-005',
                'customer_id' => $customers->skip(3)->first() ? $customers->skip(3)->first()->id : null,
                'cashier_id' => $cashiers->first() ? $cashiers->first()->id : null,
                'sale_date' => Carbon::now(),
                'subtotal' => 3200.00,
                'tax_amount' => 224.00,
                'discount_amount' => 200.00,
                'total_amount' => 3224.00,
                'status' => 'pending',
                'payment_method' => 'credit',
                'notes' => 'ขายเครดิต รอชำระเงิน',
                'created_at' => Carbon::now()
            ]
        ];

        foreach ($sales as $saleData) {
            $sale = Sale::create($saleData);
            
            // Create sale items for each sale
            if ($products->isNotEmpty()) {
                $numItems = rand(1, 3); // 1-3 items per sale
                $selectedProducts = $products->random($numItems);
                
                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 5);
                    $unitPrice = $product->selling_price ?? 100;
                    $totalPrice = $quantity * $unitPrice;
                    
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'discount_amount' => 0
                    ]);
                }
            }
        }
    }
}
