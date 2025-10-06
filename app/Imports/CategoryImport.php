<?php

namespace App\Imports;

use App\Models\ProductCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    private $successCount = 0;
    private $errorCount = 0;

    public function model(array $row)
    {
        // ตรวจสอบว่า parent_id มีอยู่จริงหรือไม่ (ถ้าไม่ใช่ null)
        if (!empty($row['หมวดหมู่แม่_id']) && !ProductCategory::find($row['หมวดหมู่แม่_id'])) {
            $this->errorCount++;
            return null;
        }

        // ตรวจสอบว่าชื่อหมวดหมู่ซ้ำหรือไม่
        if (ProductCategory::where('name', $row['ชื่อหมวดหมู่'])->exists()) {
            $this->errorCount++;
            return null;
        }

        $this->successCount++;

        return new ProductCategory([
            'name' => $row['ชื่อหมวดหมู่'],
            'description' => $row['คำอธิบาย'] ?? null,
            'parent_id' => !empty($row['หมวดหมู่แม่_id']) ? $row['หมวดหมู่แม่_id'] : null,
            'is_active' => $row['สถานะการใช้งาน'] ?? 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'ชื่อหมวดหมู่' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_categories', 'name')
            ],
            'คำอธิบาย' => 'nullable|string|max:500',
            'หมวดหมู่แม่_id' => [
                'nullable',
                'integer',
                'exists:product_categories,id'
            ],
            'สถานะการใช้งาน' => 'nullable|boolean'
        ];
    }

    public function customValidationMessages()
    {
        return [
            'ชื่อหมวดหมู่.required' => 'ชื่อหมวดหมู่เป็นข้อมูลที่จำเป็น',
            'ชื่อหมวดหมู่.unique' => 'ชื่อหมวดหมู่นี้มีอยู่แล้วในระบบ',
            'ชื่อหมวดหมู่.max' => 'ชื่อหมวดหมู่ต้องไม่เกิน 255 ตัวอักษร',
            'คำอธิบาย.max' => 'คำอธิบายต้องไม่เกิน 500 ตัวอักษร',
            'หมวดหมู่แม่_id.exists' => 'หมวดหมู่แม่ที่ระบุไม่มีอยู่ในระบบ',
            'หมวดหมู่แม่_id.integer' => 'หมวดหมู่แม่_ID ต้องเป็นตัวเลข',
            'สถานะการใช้งาน.boolean' => 'สถานะการใช้งานต้องเป็น 0 หรือ 1'
        ];
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getErrorCount(): int
    {
        return $this->errorCount;
    }

    public function hasErrors(): bool
    {
        return count($this->errors()) > 0 || count($this->failures()) > 0;
    }

    public function getErrors(): array
    {
        $allErrors = [];
        
        // รวม validation errors
        foreach ($this->errors() as $error) {
            $allErrors[] = $error->getMessage();
        }
        
        // รวม failure errors
        foreach ($this->failures() as $failure) {
            $allErrors[] = "แถว {$failure->row()}: " . implode(', ', $failure->errors());
        }
        
        return $allErrors;
    }
}