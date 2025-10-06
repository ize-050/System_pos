<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\ReceiptSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

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
            $qrCode = base64_encode(QrCode::format('png')
                ->size(200)
                ->generate($settings->promptpay_number));
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
    public function print($saleId)
    {
        $sale = Sale::with(['customer', 'saleItems.product', 'cashier'])
            ->findOrFail($saleId);

        $settings = ReceiptSettings::getSettings();

        // Generate QR Code
        $qrCode = null;
        if ($settings->show_qr_code && $settings->promptpay_number) {
            $qrCode = base64_encode(QrCode::format('png')
                ->size(200)
                ->generate($settings->promptpay_number));
        }

        return view('receipts.print', [
            'sale' => $sale,
            'settings' => $settings,
            'qrCode' => $qrCode,
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
            $qrCode = base64_encode(QrCode::format('png')
                ->size(200)
                ->generate($settings->promptpay_number));
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
            $qrCode = base64_encode(QrCode::format('png')
                ->size(200)
                ->generate($settings->promptpay_number));
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
