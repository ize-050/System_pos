<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyReportSnapshotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_report_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('report_date')->unique();
            $table->decimal('sales_total', 15, 2)->default(0);
            $table->decimal('purchase_total', 15, 2)->default(0);
            $table->decimal('profit_loss', 15, 2)->default(0);
            $table->integer('transaction_count')->default(0);
            $table->timestamp('generated_at');
            $table->timestamps();
            
            $table->index('report_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_report_snapshots');
    }
}
