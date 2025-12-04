<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixInventoryMovementsAndReceiptSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Fix inventory_movements - make user_id nullable
        DB::statement("ALTER TABLE inventory_movements MODIFY COLUMN user_id BIGINT UNSIGNED NULL");
        
        // Fix receipt_settings - change printer_type to VARCHAR
        DB::statement("ALTER TABLE receipt_settings MODIFY COLUMN printer_type VARCHAR(50) DEFAULT 'a4'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert if needed
    }
}
