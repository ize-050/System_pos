<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExportJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('export_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('report_type', 50); // sales, purchase, inventory, product
            $table->string('file_format', 20)->default('xlsx'); // xlsx, pdf
            $table->string('status', 20)->default('pending'); // pending, processing, completed, failed
            $table->string('file_path')->nullable();
            $table->text('filters_applied')->nullable(); // JSON string
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('export_jobs');
    }
}
