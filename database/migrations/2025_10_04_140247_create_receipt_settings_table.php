<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipt_settings', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name')->default('ร้านค้า');
            $table->text('shop_address')->nullable();
            $table->string('shop_phone', 50)->nullable();
            $table->string('shop_email', 100)->nullable();
            $table->string('shop_website')->nullable();
            $table->string('shop_facebook')->nullable();
            $table->string('shop_line_id', 100)->nullable();
            $table->string('tax_id', 50)->nullable()->comment('เลขประจำตัวผู้เสียภาษี');
            $table->string('logo_path')->nullable();
            $table->string('promptpay_number', 50)->nullable();
            $table->string('promptpay_name')->nullable();
            $table->boolean('show_logo')->default(true);
            $table->boolean('show_customer_info')->default(true);
            $table->boolean('show_vat')->default(true);
            $table->boolean('show_qr_code')->default(true);
            $table->boolean('show_barcode')->default(true);
            $table->boolean('show_notes')->default(true);
            $table->text('receipt_notes')->nullable();
            $table->enum('printer_type', ['a4', 'thermal_80mm', 'thermal_58mm'])->default('a4');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receipt_settings');
    }
}
