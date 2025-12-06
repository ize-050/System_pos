<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\ReceiptSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ReceiptController extends Controller
{
    /**
     * Preview receipt for a sale.
     */
    public function preview($saleId)
    {
        $sale = Sale::with(['customer', 'saleItems.product', 'cashier'])
            ->findOrFail($saleId);

        $settings = ReceiptSettings::getSettings();

        // Generate QR Code for PromptPay
        $qrCode = null;
        if ($settings->show_qr_code && $settings->promptpay_number) {
            try {
                $qrCode = base64_encode(QrCode::format('png')
                    ->size(200)
                    ->generate($settings->promptpay_number));
            } catch (\Exception $e) {
                Log::warning('QR Code generation failed: ' . $e->getMessage());
                $qrCode = null;
            }
        }

        // Generate Barcode for sale number
        $barcode = null;
        if ($settings->show_barcode) {
            $barcode = $sale->sale_number;
        }

        return Inertia::render('Receipts/Preview', [
            'sale' => $sale,
            'settings' => $settings,
            'qrCode' => $qrCode,
            'barcode' => $barcode,
        ]);
    }

    /**
     * Print receipt (optimized view).
     */
    public function print(Request $request, $saleId)
    {
        $sale = Sale::with(['customer', 'saleItems.product', 'cashier'])
            ->findOrFail($saleId);

        $settings = ReceiptSettings::getSettings();

        // Check if tax invoice type is requested (from query param or sale's invoice_type)
        $invoiceType = $request->query('type', $sale->invoice_type ?? 'cash_bill');
        
        // Override sale's invoice_type to match the requested type
        $sale->invoice_type = $invoiceType;
        
        // Override printer type for tax invoice
        if ($invoiceType === 'tax_invoice') {
            $settings->printer_type = 'dot_matrix';
        }

        // Generate QR Code
        $qrCode = null;
        if ($settings->show_qr_code && $settings->promptpay_number) {
            try {
                $qrCode = base64_encode(QrCode::format('png')
                    ->size(200)
                    ->generate($settings->promptpay_number));
            } catch (\Exception $e) {
                // If QR code generation fails, just skip it
                Log::warning('QR Code generation failed: ' . $e->getMessage());
                $qrCode = null;
            }
        }

        return Inertia::render('Sales/Receipt', [
            'sale' => $sale,
            'settings' => $settings,
            'qrCode' => $qrCode,
            'invoiceType' => $invoiceType,
        ]);
    }

    /**
     * Download receipt as PDF.
     */
    public function downloadPDF($saleId)
    {
        $sale = Sale::with(['customer', 'saleItems.product', 'cashier'])
            ->findOrFail($saleId);

        $settings = ReceiptSettings::getSettings();

        // Generate QR Code
        $qrCode = null;
        if ($settings->show_qr_code && $settings->promptpay_number) {
            try {
                $qrCode = base64_encode(QrCode::format('png')
                    ->size(200)
                    ->generate($settings->promptpay_number));
            } catch (\Exception $e) {
                Log::warning('QR Code generation failed: ' . $e->getMessage());
                $qrCode = null;
            }
        }

        $pdf = Pdf::loadView('receipts.pdf', [
            'sale' => $sale,
            'settings' => $settings,
            'qrCode' => $qrCode,
        ]);

        return $pdf->download('receipt-' . $sale->sale_number . '.pdf');
    }

    /**
     * Send receipt via email.
     */
    public function sendEmail(Request $request, $saleId)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $sale = Sale::with(['customer', 'saleItems.product', 'cashier'])
            ->findOrFail($saleId);

        $settings = ReceiptSettings::getSettings();

        // Generate QR Code
        $qrCode = null;
        if ($settings->show_qr_code && $settings->promptpay_number) {
            try {
                $qrCode = base64_encode(QrCode::format('png')
                    ->size(200)
                    ->generate($settings->promptpay_number));
            } catch (\Exception $e) {
                Log::warning('QR Code generation failed: ' . $e->getMessage());
                $qrCode = null;
            }
        }

        // Generate PDF
        $pdf = Pdf::loadView('receipts.pdf', [
            'sale' => $sale,
            'settings' => $settings,
            'qrCode' => $qrCode,
        ]);

        // Send email
        Mail::send('emails.receipt', ['sale' => $sale], function ($message) use ($validated, $pdf, $sale) {
            $message->to($validated['email'])
                ->subject('Receipt - ' . $sale->sale_number)
                ->attachData($pdf->output(), 'receipt-' . $sale->sale_number . '.pdf');
        });

        return back()->with('success', 'Receipt sent to ' . $validated['email']);
    }
}
