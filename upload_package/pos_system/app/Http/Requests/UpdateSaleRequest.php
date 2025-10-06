<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow admin and manager roles to update sales
        return auth()->check() && in_array(auth()->user()->role, ['admin', 'manager']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total_price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,bank_transfer,e_wallet',
            'payment_received' => 'required|numeric|min:0',
            'change_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'At least one item is required for the sale.',
            'items.*.product_id.required' => 'Product is required for each item.',
            'items.*.product_id.exists' => 'Selected product does not exist.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.unit_price.required' => 'Unit price is required for each item.',
            'items.*.unit_price.min' => 'Unit price must be greater than or equal to 0.',
            'items.*.total_price.required' => 'Total price is required for each item.',
            'items.*.total_price.min' => 'Total price must be greater than or equal to 0.',
            'subtotal.required' => 'Subtotal is required.',
            'subtotal.min' => 'Subtotal must be greater than or equal to 0.',
            'tax_amount.required' => 'Tax amount is required.',
            'tax_amount.min' => 'Tax amount must be greater than or equal to 0.',
            'discount_amount.min' => 'Discount amount must be greater than or equal to 0.',
            'total_amount.required' => 'Total amount is required.',
            'total_amount.min' => 'Total amount must be greater than or equal to 0.',
            'payment_method.required' => 'Payment method is required.',
            'payment_method.in' => 'Invalid payment method selected.',
            'payment_received.required' => 'Payment received amount is required.',
            'payment_received.min' => 'Payment received must be greater than or equal to 0.',
            'change_amount.min' => 'Change amount must be greater than or equal to 0.',
            'customer_email.email' => 'Please enter a valid email address.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate that payment received is sufficient
            if ($this->payment_received < $this->total_amount) {
                $validator->errors()->add('payment_received', 'Payment received must be greater than or equal to total amount.');
            }

            // Validate change amount calculation
            $expectedChange = $this->payment_received - $this->total_amount;
            if (abs($this->change_amount - $expectedChange) > 0.01) {
                $validator->errors()->add('change_amount', 'Change amount calculation is incorrect.');
            }

            // Validate total amount calculation
            $expectedTotal = $this->subtotal + $this->tax_amount - ($this->discount_amount ?? 0);
            if (abs($this->total_amount - $expectedTotal) > 0.01) {
                $validator->errors()->add('total_amount', 'Total amount calculation is incorrect.');
            }
        });
    }
}
