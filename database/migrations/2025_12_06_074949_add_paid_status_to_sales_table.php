<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPaidStatusToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add 'paid' to status enum for credit sales that have been paid
        DB::statement("ALTER TABLE sales MODIFY COLUMN status ENUM('pending', 'completed', 'cancelled', 'refunded', 'partial_refunded', 'paid') DEFAULT 'completed'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE sales MODIFY COLUMN status ENUM('pending', 'completed', 'cancelled', 'refunded', 'partial_refunded') DEFAULT 'completed'");
    }
}
