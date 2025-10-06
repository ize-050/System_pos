<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductCategory;

$categories = ProductCategory::select('id', 'name', 'description', 'parent_id', 'is_active')
    ->orderBy('id')
    ->get()
    ->toArray();

echo "หมวดหมู่สินค้าทั้งหมด:\n";
echo "ID | ชื่อหมวดหมู่ | คำอธิบาย | หมวดหมู่แม่ | สถานะ\n";
echo str_repeat("-", 80) . "\n";

foreach ($categories as $category) {
    printf(
        "%2d | %-20s | %-25s | %8s | %s\n",
        $category['id'],
        $category['name'],
        $category['description'] ?? '-',
        $category['parent_id'] ?? 'หลัก',
        $category['is_active'] ? 'ใช้งาน' : 'ไม่ใช้งาน'
    );
}

echo "\nจำนวนหมวดหมู่ทั้งหมด: " . count($categories) . " หมวดหมู่\n";