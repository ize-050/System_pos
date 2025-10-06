<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'manager');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $productId = $this->route('product')->id ?? $this->route('product');
        
        return [
            'sku' => 'required|string|max:50|unique:products,sku,' . $productId,
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:product_categories,id',
            'unit' => 'required|string|max:20',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:cost_price',
            'current_stock' => 'required|integer|min:0',
            'reorder_point' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
            // New construction fields
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'material' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'warranty_period' => 'nullable|integer|min:0',
            'supplier' => 'nullable|string|max:200',
            'origin_country' => 'nullable|string|max:100',
            'specifications' => 'nullable|string',
            'usage_instructions' => 'nullable|string',
            'safety_warnings' => 'nullable|string',
            'storage_conditions' => 'nullable|string',
            'manufacturing_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:manufacturing_date',
            'certification' => 'nullable|string|max:200'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'sku.required' => 'รหัสสินค้าจำเป็นต้องระบุ',
            'sku.unique' => 'รหัสสินค้านี้มีอยู่แล้ว',
            'name.required' => 'ชื่อสินค้าจำเป็นต้องระบุ',
            'category_id.required' => 'หมวดหมู่จำเป็นต้องระบุ',
            'category_id.exists' => 'หมวดหมู่ที่เลือกไม่มีอยู่',
            'cost_price.required' => 'ราคาต้นทุนจำเป็นต้องระบุ',
            'cost_price.min' => 'ราคาต้นทุนต้องมากกว่าหรือเท่ากับ 0',
            'selling_price.required' => 'ราคาขายจำเป็นต้องระบุ',
            'selling_price.min' => 'ราคาขายต้องมากกว่าหรือเท่ากับ 0',
            'selling_price.gte' => 'ราคาขายต้องมากกว่าหรือเท่ากับราคาต้นทุน',
            'current_stock.required' => 'จำนวนสต็อกปัจจุบันจำเป็นต้องระบุ',
            'current_stock.min' => 'จำนวนสต็อกปัจจุบันต้องมากกว่าหรือเท่ากับ 0',
            'reorder_point.required' => 'จุดสั่งซื้อใหม่จำเป็นต้องระบุ',
            'reorder_point.min' => 'จุดสั่งซื้อใหม่ต้องมากกว่าหรือเท่ากับ 0',
            'image.image' => 'ไฟล์ต้องเป็นรูปภาพ',
            'image.mimes' => 'รูปภาพต้องเป็นไฟล์ jpeg, png, jpg หรือ gif',
            'image.max' => 'ขนาดรูปภาพต้องไม่เกิน 2MB',
            'weight.min' => 'น้ำหนักต้องมากกว่าหรือเท่ากับ 0',
            'warranty_period.min' => 'ระยะเวลาการรับประกันต้องมากกว่าหรือเท่ากับ 0',
            'expiry_date.after' => 'วันหมดอายุต้องมาหลังวันที่ผลิต'
        ];
    }
}
