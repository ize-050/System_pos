<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code', 20)->unique();
            $table->string('name', 100); // ชื่อลูกค้า
            $table->string('phone', 20); // เบอร์โทร
            $table->string('line_id', 100)->nullable(); // Line ID
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('company_name', 100)->nullable();
            $table->string('tax_id', 20)->nullable();
            $table->decimal('credit_limit', 12, 2)->default(0.00);
            $table->decimal('current_balance', 12, 2)->default(0.00);
            $table->integer('payment_terms')->default(30);
            $table->enum('customer_type', ['individual', 'company', 'contractor'])->default('individual');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['customer_code']);
            $table->index(['phone']);
            $table->index(['name']);
            $table->index(['line_id']);
            $table->index(['customer_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
