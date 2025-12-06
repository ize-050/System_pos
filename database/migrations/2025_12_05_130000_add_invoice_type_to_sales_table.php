<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceTypeToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('invoice_type', 20)->default('cash_bill')->after('status');
            $table->text('tax_invoice_customer')->nullable()->after('invoice_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['invoice_type', 'tax_invoice_customer']);
        });
    }
}
