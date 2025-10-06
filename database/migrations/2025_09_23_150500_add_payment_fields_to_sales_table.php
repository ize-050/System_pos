<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('received_amount', 12, 2)->nullable()->after('payment_method');
            $table->decimal('change_amount', 12, 2)->nullable()->after('received_amount');
            $table->foreignId('promotion_id')->nullable()->constrained('promotions')->after('change_amount');
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
            $table->dropForeign(['promotion_id']);
            $table->dropColumn(['received_amount', 'change_amount', 'promotion_id']);
        });
    }
}