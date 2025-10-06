<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConstructionFieldsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // ข้อมูลเพิ่มเติมสำหรับอุปกรณ์ก่อสร้าง
            $table->string('brand', 100)->nullable()->after('unit'); // ยี่ห้อ
            $table->string('model', 100)->nullable()->after('brand'); // รุ่น
            $table->string('size', 50)->nullable()->after('model'); // ขนาด
            $table->string('color', 50)->nullable()->after('size'); // สี
            $table->string('material', 100)->nullable()->after('color'); // วัสดุ
            $table->decimal('weight', 8, 2)->nullable()->after('material'); // น้ำหนัก (กก.)
            $table->string('dimensions', 100)->nullable()->after('weight'); // ขนาด (กว้าง x ยาว x สูง)
            $table->string('warranty_period', 50)->nullable()->after('dimensions'); // ระยะเวลาการรับประกัน
            $table->string('supplier', 100)->nullable()->after('warranty_period'); // ผู้จำหน่าย
            $table->string('origin_country', 50)->nullable()->after('supplier'); // ประเทศผู้ผลิต
            $table->text('specifications')->nullable()->after('origin_country'); // รายละเอียดเทคนิค
            $table->text('usage_instructions')->nullable()->after('specifications'); // คำแนะนำการใช้งาน
            $table->text('safety_warnings')->nullable()->after('usage_instructions'); // คำเตือนด้านความปลอดภัย
            $table->string('storage_conditions', 200)->nullable()->after('safety_warnings'); // เงื่อนไขการเก็บรักษา
            $table->date('manufacturing_date')->nullable()->after('storage_conditions'); // วันที่ผลิต
            $table->date('expiry_date')->nullable()->after('manufacturing_date'); // วันหมดอายุ
            $table->string('certification', 200)->nullable()->after('expiry_date'); // มาตรฐาน/ใบรับรอง
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'brand',
                'model', 
                'size',
                'color',
                'material',
                'weight',
                'dimensions',
                'warranty_period',
                'supplier',
                'origin_country',
                'specifications',
                'usage_instructions',
                'safety_warnings',
                'storage_conditions',
                'manufacturing_date',
                'expiry_date',
                'certification'
            ]);
        });
    }
}
