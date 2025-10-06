<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 50)->unique()->comment('เลขที่ PO: PO-YYYY-XXXX');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->date('order_date')->comment('วันที่สั่งซื้อ');
            $table->date('expected_delivery_date')->nullable()->comment('วันที่คาดว่าจะได้รับ');
            $table->date('received_date')->nullable()->comment('วันที่รับสินค้าจริง');
            $table->decimal('subtotal', 15, 2)->default(0)->comment('ยอดรวมก่อน VAT');
            $table->decimal('discount_amount', 15, 2)->default(0)->comment('ส่วนลด');
            $table->decimal('vat_amount', 15, 2)->default(0)->comment('ภาษีมูลค่าเพิ่ม 7%');
            $table->decimal('total_amount', 15, 2)->default(0)->comment('ยอดรวมสุทธิ');
            $table->enum('status', ['draft', 'pending', 'shipping', 'received', 'cancelled'])->default('draft');
            $table->text('notes')->nullable()->comment('หมายเหตุ');
            $table->string('document_path')->nullable()->comment('เอกสารแนบ');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null')->comment('ผู้รับสินค้า');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('supplier_id');
            $table->index('status');
            $table->index('order_date');
            $table->index('po_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}
