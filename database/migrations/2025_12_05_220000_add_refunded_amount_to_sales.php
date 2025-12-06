<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add refunded_amount column
        if (!Schema::hasColumn('sales', 'refunded_amount')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->decimal('refunded_amount', 12, 2)->default(0)->after('total_amount');
            });
        }

        // Update status enum to include partial_refunded
        DB::statement("ALTER TABLE sales MODIFY COLUMN status ENUM('pending', 'completed', 'cancelled', 'refunded', 'partial_refunded') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('refunded_amount');
        });

        DB::statement("ALTER TABLE sales MODIFY COLUMN status ENUM('pending', 'completed', 'cancelled', 'refunded') DEFAULT 'pending'");
    }
};
