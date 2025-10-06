<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add notes column to sale_items table
        Schema::table('sale_items', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('discount_amount');
        });

        // Add usage_count column to promotions table
        Schema::table('promotions', function (Blueprint $table) {
            $table->integer('usage_count')->default(0)->after('usage_limit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn('notes');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn('usage_count');
        });
    }
}
