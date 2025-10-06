<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;

class ProductImport implements ToCollection, WithHeadingRow
{
    use Importable;

    protected $errors = [];
    protected $successCount = 0;
    protected $errorCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of header row and 0-based index
            
            try {
                // Validate required fields
                $validator = Validator::make($row->toArray(), [
                    'ชื่อสินค้า' => 'required|string|max:255',
                    'หมวดหมู่_id' => 'required|integer|exists:product_categories,id',
                    'ราคาทุน' => 'required|numeric|min:0',
                    'ราคาขาย' => 'required|numeric|min:0',
                ]);

                if ($validator->fails()) {
                    $this->errors[] = "แถว {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    $this->errorCount++;
                    continue;
                }

                // Check if category exists and is active
                $category = ProductCategory::where('id', $row['หมวดหมู่_id'])
                    ->where('is_active', true)
                    ->first();

                if (!$category) {
                    $this->errors[] = "แถว {$rowNumber}: หมวดหมู่ ID {$row['หมวดหมู่_id']} ไม่พบหรือไม่ได้เปิดใช้งาน";
                    $this->errorCount++;
                    continue;
                }

                // Check for duplicate SKU if provided
                if (!empty($row['รหัสสินค้า_sku'])) {
                    $existingProduct = Product::where('sku', $row['รหัสสินค้า_sku'])->first();
                    if ($existingProduct) {
                        $this->errors[] = "แถว {$rowNumber}: รหัสสินค้า {$row['รหัสสินค้า_sku']} มีอยู่แล้ว";
                        $this->errorCount++;
                        continue;
                    }
                }

                // Check for duplicate barcode if provided
                if (!empty($row['บาร์โค้ด'])) {
                    $existingProduct = Product::where('barcode', $row['บาร์โค้ด'])->first();
                    if ($existingProduct) {
                        $this->errors[] = "แถว {$rowNumber}: บาร์โค้ด {$row['บาร์โค้ด']} มีอยู่แล้ว";
                        $this->errorCount++;
                        continue;
                    }
                }

                // Prepare product data
                $productData = [
                    'sku' => $row['รหัสสินค้า_sku'] ?? null,
                    'barcode' => $row['บาร์โค้ด'] ?? null,
                    'name' => $row['ชื่อสินค้า'],
                    'description' => $row['รายละเอียด'] ?? null,
                    'category_id' => $row['หมวดหมู่_id'],
                    'unit' => $row['หน่วย'] ?? 'ชิ้น',
                    'brand' => $row['ยี่ห้อ'] ?? null,
                    'model' => $row['รุ่น'] ?? null,
                    'size' => $row['ขนาด'] ?? null,
                    'color' => $row['สี'] ?? null,
                    'material' => $row['วัสดุ'] ?? null,
                    'weight' => !empty($row['น้ำหนัก_กก']) ? (float)$row['น้ำหนัก_กก'] : null,
                    'dimensions' => $row['ขนาด_กว้าง_x_ยาว_x_สูง'] ?? null,
                    'warranty_period' => $row['ระยะเวลาการรับประกัน'] ?? null,
                    'supplier' => $row['ผู้จำหน่าย'] ?? null,
                    'origin_country' => $row['ประเทศต้นกำเนิด'] ?? null,
                    'specifications' => $row['ข้อมูลจำเพาะ'] ?? null,
                    'usage_instructions' => $row['คำแนะนำการใช้งาน'] ?? null,
                    'safety_warnings' => $row['คำเตือนด้านความปลอดภัย'] ?? null,
                    'storage_conditions' => $row['เงื่อนไขการเก็บรักษา'] ?? null,
                    'manufacturing_date' => !empty($row['วันที่ผลิต_yyyy_mm_dd']) ? $row['วันที่ผลิต_yyyy_mm_dd'] : null,
                    'expiry_date' => !empty($row['วันหมดอายุ_yyyy_mm_dd']) ? $row['วันหมดอายุ_yyyy_mm_dd'] : null,
                    'certification' => $row['การรับรอง'] ?? null,
                    'cost_price' => (float)$row['ราคาทุน'],
                    'selling_price' => (float)$row['ราคาขาย'],
                    'current_stock' => !empty($row['สต็อกปัจจุบัน']) ? (int)$row['สต็อกปัจจุบัน'] : 0,
                    'reorder_point' => !empty($row['จุดสั่งซื้อใหม่']) ? (int)$row['จุดสั่งซื้อใหม่'] : 0,
                    'is_active' => isset($row['เปิดใช้งาน_1_เปิด_0_ปิด']) ? (bool)$row['เปิดใช้งาน_1_เปิด_0_ปิด'] : true,
                    'notes' => $row['หมายเหตุ'] ?? null,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ];

                // Create the product
                Product::create($productData);
                $this->successCount++;

            } catch (\Exception $e) {
                $this->errors[] = "แถว {$rowNumber}: เกิดข้อผิดพลาด - " . $e->getMessage();
                $this->errorCount++;
            }
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getErrorCount()
    {
        return $this->errorCount;
    }

    public function hasErrors()
    {
        return count($this->errors) > 0;
    }
}