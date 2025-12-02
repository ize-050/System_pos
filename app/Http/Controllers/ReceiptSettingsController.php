<?php

namespace App\Http\Controllers;

use App\Models\ReceiptSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ReceiptSettingsController extends Controller
{
    /**
     * Show the form for editing receipt settings.
     */
    public function edit()
    {
        $settings = ReceiptSettings::getSettings();

        return Inertia::render('Receipts/Settings', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update receipt settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_address' => 'nullable|string',
            'shop_phone' => 'nullable|string|max:50',
            'shop_email' => 'nullable|email|max:100',
            'shop_website' => 'nullable|url|max:255',
            'shop_facebook' => 'nullable|string|max:255',
            'shop_line_id' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:50',
            'logo' => 'nullable|image|max:2048',
            'promptpay_number' => 'nullable|string|max:50',
            'promptpay_name' => 'nullable|string|max:255',
            'show_logo' => 'boolean',
            'show_customer_info' => 'boolean',
            'show_vat' => 'boolean',
            'show_qr_code' => 'boolean',
            'show_barcode' => 'boolean',
            'show_notes' => 'boolean',
            'receipt_notes' => 'nullable|string',
            'printer_type' => 'required|in:a4,thermal_80mm,thermal_58mm',
        ]);

        $settings = ReceiptSettings::getSettings();

        // Handle logo removal
        if ($request->input('remove_logo')) {
            if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $validated['logo_path'] = null;
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
                Storage::disk('public')->delete($settings->logo_path);
            }

            // Store new logo
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = $logoPath;
        }

        $settings->update($validated);

        return back()->with('success', 'Receipt settings updated successfully');
    }
}
