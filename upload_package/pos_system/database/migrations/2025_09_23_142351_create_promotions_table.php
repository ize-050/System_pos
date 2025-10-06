<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ชื่อโปรโมชั่น
            $table->text('description')->nullable(); // รายละเอียดโปรโมชั่น
            $table->enum('type', ['percentage', 'fixed_amount', 'buy_x_get_y']); // ประเภทส่วนลด
            $table->decimal('value', 10, 2)->nullable(); // ค่าส่วนลด (% หรือ จำนวนเงิน)
            $table->integer('min_quantity')->nullable(); // จำนวนขั้นต่ำ (สำหรับ buy_x_get_y)
            $table->integer('free_quantity')->nullable(); // จำนวนที่ได้ฟรี (สำหรับ buy_x_get_y)
            $table->decimal('min_amount', 10, 2)->nullable(); // ยอดขั้นต่ำ
            $table->decimal('max_discount', 10, 2)->nullable(); // ส่วนลดสูงสุด
            $table->date('start_date'); // วันที่เริ่มต้น
            $table->date('end_date'); // วันที่สิ้นสุด
            $table->boolean('is_active')->default(true); // สถานะใช้งาน
            $table->json('applicable_products')->nullable(); // สินค้าที่ใช้ได้ (array of product_ids)
            $table->json('applicable_categories')->nullable(); // หมวดหมู่ที่ใช้ได้ (array of category_ids)
            $table->integer('usage_limit')->nullable(); // จำนวนครั้งที่ใช้ได้
            $table->integer('used_count')->default(0); // จำนวนครั้งที่ใช้แล้ว
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
        Schema::dropIfExists('promotions');
    }
}
