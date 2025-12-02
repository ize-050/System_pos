<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ImportProductTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:product-template
        {--path=product_template.xlsx : Relative path to the Excel template}
        {--truncate : Truncate related tables before importing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import product categories and products from an Excel template';

    public function handle(): int
    {
        $path = base_path($this->option('path'));

        if (! file_exists($path)) {
            $this->error("ไม่พบไฟล์ {$path}");
            return self::FAILURE;
        }

        try {
            $spreadsheet = IOFactory::load($path);
        } catch (\Throwable $e) {
            $this->error('ไม่สามารถเปิดไฟล์ Excel: ' . $e->getMessage());
            return self::FAILURE;
        }

        if ($this->option('truncate')) {
            $this->truncateTables();
        }

        DB::beginTransaction();

        try {
            $categorySheet = $spreadsheet->getSheetByName('หมวดหมู่สินค้า');
            if (! $categorySheet instanceof Worksheet) {
                throw new \RuntimeException('ไม่พบชีต "หมวดหมู่สินค้า" ในไฟล์ Excel');
            }
            $categoryRows = $this->normalizeSheet($categorySheet);
            $insertedCategories = $this->importCategories($categoryRows);

            $productSheet = $spreadsheet->getSheetByName('สินค้า');
            if (! $productSheet instanceof Worksheet) {
                throw new \RuntimeException('ไม่พบชีต "สินค้า" ในไฟล์ Excel');
            }
            $productRows = $this->normalizeSheet($productSheet);
            $insertedProducts = $this->importProducts($productRows);

            DB::commit();

            $this->info("นำเข้าหมวดหมู่ {$insertedCategories} รายการ และสินค้า {$insertedProducts} รายการเรียบร้อยแล้ว");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('เกิดข้อผิดพลาดระหว่างนำเข้าข้อมูล: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function truncateTables(): void
    {
        $this->warn('กำลังล้างข้อมูลที่เกี่ยวข้อง...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('sale_items')->truncate();
        DB::table('purchase_order_items')->truncate();
        DB::table('purchase_orders')->truncate();
        DB::table('inventory_movements')->truncate();
        DB::table('products')->truncate();
        DB::table('product_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function importCategories(array $rows): int
    {
        $now = Carbon::now();
        $inserted = 0;

        foreach ($rows as $row) {
            $id = isset($row['ID']) ? (int) $row['ID'] : null;
            $name = $row['ชื่อหมวดหมู่'] ?? null;

            if (! $name) {
                $this->warn('ข้ามหมวดหมู่ที่ไม่มีชื่อ');
                continue;
            }

            $isActive = true;
            if (isset($row['สถานะ'])) {
                $status = trim((string) $row['สถานะ']);
                $isActive = ! in_array(mb_strtolower($status), ['ปิด', 'ไม่ใช้งาน', 'inactive'], true);
            }

            $parent = $row['หมวดหมู่แม่'] ?? null;
            $parentId = null;
            if ($parent) {
                $parentText = trim((string) $parent);
                if ($parentText !== '' && mb_strtolower($parentText) !== 'หลัก') {
                    $parentId = is_numeric($parentText) ? (int) $parentText : null;
                }
            }

            $payload = [
                'name' => $name,
                'description' => $row['คำอธิบาย'] ?? null,
                'parent_id' => $parentId,
                'is_active' => $isActive,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($id) {
                $payload['id'] = $id;
            }

            DB::table('product_categories')->insert($payload);
            $inserted++;
        }

        return $inserted;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function importProducts(array $rows): int
    {
        $now = Carbon::now();
        $inserted = 0;

        $columnMap = [
            'รายละเอียด' => 'description',
            'หน่วย' => 'unit',
            'ยี่ห้อ' => 'brand',
            'รุ่น' => 'model',
            'ขนาด' => 'size',
            'สี' => 'color',
            'วัสดุ' => 'material',
            'น้ำหนัก (กก.)' => 'weight',
            'ขนาด (กว้าง x ยาว x สูง)' => 'dimensions',
            'ระยะเวลาการรับประกัน' => 'warranty_period',
            'ผู้จำหน่าย' => 'supplier',
            'ประเทศต้นกำเนิด' => 'origin_country',
            'ข้อมูลจำเพาะ' => 'specifications',
            'คำแนะนำการใช้งาน' => 'usage_instructions',
            'คำเตือนด้านความปลอดภัย' => 'safety_warnings',
            'เงื่อนไขการเก็บรักษา' => 'storage_conditions',
            'วันที่ผลิต (YYYY-MM-DD)' => 'manufacturing_date',
            'วันหมดอายุ (YYYY-MM-DD)' => 'expiry_date',
            'การรับรอง' => 'certification',
            'ราคาทุน*' => 'cost_price',
            'ราคาขาย*' => 'selling_price',
            'สต็อกปัจจุบัน' => 'current_stock',
            'จุดสั่งซื้อใหม่' => 'reorder_point',
            'เปิดใช้งาน (1=เปิด, 0=ปิด)' => 'is_active',
            'หมายเหตุ' => 'notes',
        ];

        foreach ($rows as $row) {
            $name = $row['ชื่อสินค้า*'] ?? null;
            $categoryId = isset($row['หมวดหมู่ (ID)*']) ? (int) $row['หมวดหมู่ (ID)*'] : null;

            if (! $name || ! $categoryId) {
                $this->warn('ข้ามสินค้าเพราะไม่มีชื่อหรือหมวดหมู่: ' . json_encode($row, JSON_UNESCAPED_UNICODE));
                continue;
            }

            $sku = isset($row['รหัสสินค้า (SKU)']) ? trim((string) $row['รหัสสินค้า (SKU)']) : null;
            if ($sku === '' || $sku === null) {
                $sku = 'SKU-' . Str::uuid()->toString();
            }

            $payload = [
                'sku' => $sku,
                'name' => $name,
                'category_id' => $categoryId,
                'created_at' => $now,
                'updated_at' => $now,
                'cost_price' => null,
                'selling_price' => 0,
                'current_stock' => 0,
                'reorder_point' => 0,
                'is_active' => true,
            ];

            foreach ($columnMap as $source => $destination) {
                if (! array_key_exists($source, $row)) {
                    continue;
                }

                $value = $row[$source];
                if ($value === null || $value === '') {
                    continue;
                }

                switch ($destination) {
                    case 'cost_price':
                    case 'weight':
                        $payload[$destination] = $this->toFloat($value);
                        break;
                    case 'selling_price':
                        $payload[$destination] = $this->toFloat($value);
                        break;
                    case 'current_stock':
                    case 'reorder_point':
                        $payload[$destination] = $this->toInt($value);
                        break;
                    case 'manufacturing_date':
                    case 'expiry_date':
                        $payload[$destination] = $this->toDate($value);
                        break;
                    case 'is_active':
                        $payload[$destination] = $this->toBool($value);
                        break;
                    default:
                        $payload[$destination] = is_string($value) ? trim($value) : $value;
                        break;
                }
            }

            if (is_null($payload['cost_price'])) {
                $payload['cost_price'] = $payload['selling_price'] ?? 0;
            }

            DB::table('products')->insert($payload);
            $inserted++;
        }

        return $inserted;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function normalizeSheet(Worksheet $sheet): array
    {
        $rows = $sheet->toArray(null, true, true, true);
        if (count($rows) <= 1) {
            return [];
        }

        $header = array_shift($rows);
        $normalized = [];

        foreach ($header as $column => $value) {
            $normalized[$column] = $value ? trim((string) $value) : null;
        }

        $result = [];

        foreach ($rows as $row) {
            $entry = [];
            foreach ($row as $column => $value) {
                $key = $normalized[$column] ?? null;
                if (! $key) {
                    continue;
                }

                if ($value === null) {
                    continue;
                }

                if (is_string($value)) {
                    $value = trim($value);
                }

                if ($value === '') {
                    continue;
                }

                $entry[$key] = $value;
            }

            if (! empty($entry)) {
                $result[] = $entry;
            }
        }

        return $result;
    }

    private function toFloat($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $clean = str_replace([',', ' '], '', (string) $value);
        return is_numeric($clean) ? (float) $clean : 0.0;
    }

    private function toInt($value): int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        $clean = preg_replace('/[^0-9-]/', '', (string) $value);
        return $clean !== '' ? (int) $clean : 0;
    }

    private function toDate($value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->toDateString();
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            $this->warn('ไม่สามารถแปลงค่าวันที่: ' . $value);
            return null;
        }
    }

    private function toBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $normalized = mb_strtolower(trim((string) $value));

        if ($normalized === '0' || in_array($normalized, ['false', 'ไม่', 'ปิด'], true)) {
            return false;
        }

        return true;
    }
}
