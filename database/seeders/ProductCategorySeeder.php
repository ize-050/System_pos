<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'อุปกรณ์ก่อสร้าง',
                'description' => 'อุปกรณ์และเครื่องมือสำหรับงานก่อสร้าง',
                'is_active' => true,
                'parent_id' => null,
            ],
            [
                'name' => 'วัสดุก่อสร้าง',
                'description' => 'วัสดุพื้นฐานสำหรับงานก่อสร้าง',
                'is_active' => true,
                'parent_id' => null,
            ],
            [
                'name' => 'เครื่องมือช่าง',
                'description' => 'เครื่องมือสำหรับช่างทุกประเภท',
                'is_active' => true,
                'parent_id' => null,
            ],
            [
                'name' => 'อุปกรณ์ไฟฟ้า',
                'description' => 'อุปกรณ์และเครื่องใช้ไฟฟ้า',
                'is_active' => true,
                'parent_id' => null,
            ],
            [
                'name' => 'ท่อและข้อต่อ',
                'description' => 'ท่อน้ำ ท่อแก๊ส และข้อต่อต่างๆ',
                'is_active' => true,
                'parent_id' => null,
            ],
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category);
        }

        // สร้าง subcategories
        $mainCategories = ProductCategory::whereNull('parent_id')->get();
        
        // Subcategories สำหรับอุปกรณ์ก่อสร้าง
        $constructionEquipment = $mainCategories->where('name', 'อุปกรณ์ก่อสร้าง')->first();
        if ($constructionEquipment) {
            $subCategories = [
                ['name' => 'เครื่องมือไฟฟ้า', 'description' => 'สว่าน เลื่อย เครื่องขัด', 'parent_id' => $constructionEquipment->id],
                ['name' => 'เครื่องมือมือ', 'description' => 'ค้อน ไขควง คีม', 'parent_id' => $constructionEquipment->id],
                ['name' => 'อุปกรณ์วัด', 'description' => 'ตลับเมตร ระดับน้ำ', 'parent_id' => $constructionEquipment->id],
            ];
            
            foreach ($subCategories as $subCategory) {
                ProductCategory::create($subCategory + ['is_active' => true]);
            }
        }

        // Subcategories สำหรับวัสดุก่อสร้าง
        $constructionMaterials = $mainCategories->where('name', 'วัสดุก่อสร้าง')->first();
        if ($constructionMaterials) {
            $subCategories = [
                ['name' => 'ปูนซีเมนต์', 'description' => 'ปูนซีเมนต์ทุกชนิด', 'parent_id' => $constructionMaterials->id],
                ['name' => 'อิฐและบล็อก', 'description' => 'อิฐแดง อิฐมวลเบา บล็อก', 'parent_id' => $constructionMaterials->id],
                ['name' => 'เหล็กเส้น', 'description' => 'เหล็กเส้นทุกขนาด', 'parent_id' => $constructionMaterials->id],
            ];
            
            foreach ($subCategories as $subCategory) {
                ProductCategory::create($subCategory + ['is_active' => true]);
            }
        }

        // Subcategories สำหรับเครื่องมือช่าง
        $tools = $mainCategories->where('name', 'เครื่องมือช่าง')->first();
        if ($tools) {
            $subCategories = [
                ['name' => 'เครื่องมือช่างไฟ', 'description' => 'เครื่องมือสำหรับช่างไฟฟ้า', 'parent_id' => $tools->id],
                ['name' => 'เครื่องมือช่างประปา', 'description' => 'เครื่องมือสำหรับช่างประปา', 'parent_id' => $tools->id],
                ['name' => 'เครื่องมือช่างเหล็ก', 'description' => 'เครื่องมือสำหรับช่างเหล็ก', 'parent_id' => $tools->id],
            ];
            
            foreach ($subCategories as $subCategory) {
                ProductCategory::create($subCategory + ['is_active' => true]);
            }
        }
    }
}