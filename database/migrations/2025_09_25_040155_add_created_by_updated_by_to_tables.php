<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByUpdatedByToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add updated_by to customers table (created_by already exists, products and product_categories already have both)
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove updated_by from customers table (keep created_by as it was already there)
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['updated_by']);
        });
    }
}
