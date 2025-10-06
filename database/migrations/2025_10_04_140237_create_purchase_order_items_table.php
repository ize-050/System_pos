<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('po_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->string('product_name')->comment('ชื่อสินค้า');
            $table->decimal('quantity_ordered', 10, 2)->comment('จำนวนที่สั่ง');
            $table->decimal('quantity_received', 10, 2)->default(0)->comment('จำนวนที่รับจริง');
            $table->decimal('unit_price', 15, 2)->comment('ราคาต่อหน่วย');
            $table->decimal('total_price', 15, 2)->comment('ราคารวม');
            $table->enum('condition_status', ['good', 'partial_damaged', 'fully_damaged'])->default('good')->comment('สภาพสินค้า');
            $table->text('notes')->nullable()->comment('หมายเหตุ');
            $table->timestamps();

            // Indexes
            $table->index('po_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_items');
    }
}
