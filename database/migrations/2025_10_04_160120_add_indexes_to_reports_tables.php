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
        // Add indexes to purchase_orders table for better report performance
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->index(['order_date', 'status'], 'idx_po_order_date_status');
            $table->index('received_date', 'idx_po_received_date');
        });

        // Add indexes to sales table for better report performance
        Schema::table('sales', function (Blueprint $table) {
            $table->index('sale_date', 'idx_sales_sale_date');
            $table->index(['sale_date', 'payment_method'], 'idx_sales_date_payment');
        });

        // Add indexes to products table for inventory reports
        Schema::table('products', function (Blueprint $table) {
            $table->index('current_stock', 'idx_products_current_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex('idx_po_order_date_status');
            $table->dropIndex('idx_po_received_date');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('idx_sales_sale_date');
            $table->dropIndex('idx_sales_date_payment');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_current_stock');
        });
    }
};
