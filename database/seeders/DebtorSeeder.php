<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;

class DebtorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // สร้างการขายแบบเครดิตเพื่อให้เกิดลูกหนี้
        $customers = Customer::all();
        $products = Product::take(5)->get();
        $cashier = User::first();

        if ($customers->isEmpty() || $products->isEmpty() || !$cashier) {
            $this->command->info('ไม่พบข้อมูลลูกค้า สินค้า หรือผู้ใช้ กรุณาเรียกใช้ seeder อื่นก่อน');
            return;
        }

        $creditSales = [
            [
                'customer' => $customers->get(2), // บริษัท เทคโนโลยี จำกัด
                'sale_date' => Carbon::now()->subDays(45),
                'items' => [
                    ['product' => $products[0], 'quantity' => 10, 'unit_price' => 150.00],
                    ['product' => $products[1], 'quantity' => 5, 'unit_price' => 300.00],
                ],
                'notes' => 'การขายเครดิต - ครบกำหนดชำระแล้ว'
            ],
            [
                'customer' => $customers->get(0), // สมชาย ใจดี
                'sale_date' => Carbon::now()->subDays(25),
                'items' => [
                    ['product' => $products[2], 'quantity' => 8, 'unit_price' => 250.00],
                    ['product' => $products[3], 'quantity' => 3, 'unit_price' => 500.00],
                ],
                'notes' => 'การขายเครดิต - ใกล้ครบกำหนดชำระ'
            ],
            [
                'customer' => $customers->get(1), // สมหญิง รักดี
                'sale_date' => Carbon::now()->subDays(60),
                'items' => [
                    ['product' => $products[4], 'quantity' => 15, 'unit_price' => 100.00],
                    ['product' => $products[0], 'quantity' => 7, 'unit_price' => 180.00],
                ],
                'notes' => 'การขายเครดิต - เกินกำหนดชำระ'
            ]
        ];

        foreach ($creditSales as $saleData) {
            $subtotal = 0;
            foreach ($saleData['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $taxAmount = $subtotal * 0.07; // VAT 7%
            $totalAmount = $subtotal + $taxAmount;

            // สร้างการขาย
            $sale = Sale::create([
                'sale_number' => 'CREDIT-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'customer_id' => $saleData['customer']->id,
                'cashier_id' => $cashier->id,
                'sale_date' => $saleData['sale_date'],
                'payment_method' => 'credit',
                'subtotal' => $subtotal,
                'discount_amount' => 0.00,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'status' => 'completed',
                'notes' => $saleData['notes']
            ]);

            // สร้างรายการสินค้าในการขาย
            foreach ($saleData['items'] as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => 0.00,
                    'total_price' => $item['quantity'] * $item['unit_price']
                ]);
            }

            // อัพเดทยอดหนี้ของลูกค้า
            $saleData['customer']->increment('current_balance', $totalAmount);
        }

        $this->command->info('สร้างข้อมูลลูกหนี้จากการขายเครดิตเรียบร้อยแล้ว');
    }
}
