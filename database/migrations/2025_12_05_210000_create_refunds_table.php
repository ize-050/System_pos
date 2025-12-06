<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->string('refund_number')->unique(); // เลขที่ใบคืนสินค้า
            $table->foreignId('sale_id')->constrained()->onDelete('cascade'); // อ้างอิงบิลขาย
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('processed_by')->constrained('users')->onDelete('cascade'); // พนักงานที่ทำรายการ
            $table->datetime('refund_date');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('refund_method', ['cash', 'transfer', 'credit_card', 'store_credit'])->default('cash');
            $table->enum('status', ['pending', 'approved', 'completed', 'rejected'])->default('pending');
            $table->string('reason')->nullable(); // เหตุผลการคืน
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('refund_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('refund_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('sale_item_id')->nullable()->constrained()->onDelete('set null');
            $table->string('product_name');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->boolean('return_to_stock')->default(true); // คืนสต็อกหรือไม่
            $table->string('condition')->nullable(); // สภาพสินค้า: good, damaged, defective
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_items');
        Schema::dropIfExists('refunds');
    }
};
