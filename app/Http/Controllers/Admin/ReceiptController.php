<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualReceipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function manualCreate()
    {
        $maxPayment = DB::table('payments')
            ->where('invoice_number', 'like', 'INV-%')
            ->max('invoice_number');

        $maxManual = DB::table('manual_receipts')
            ->where('invoice_number', 'like', 'INV-%')
            ->max('invoice_number');

        $lastInvoice = ($maxPayment >= $maxManual) ? $maxPayment : $maxManual;
        
        $today = date('Ymd');
        $nextNumber = 1;

        if ($lastInvoice) {
            $parts = explode('-', $lastInvoice);
            
            if (count($parts) === 3) {
                $lastDate = $parts[1];
                $lastSequence = (int) $parts[2];

                if ($lastDate == $today) {
                    $nextNumber = $lastSequence + 1;
                } else {
                    $nextNumber = $lastSequence + 1;
                }
            }
        }

        $newInvoiceNumber = 'INV-' . $today . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('admin.receipt.manual_form', compact('newInvoiceNumber'));
    }

    public function manualPrint($id) {
        $payment = ManualReceipt::findOrFail($id);
        return view('admin.receipt.manual_print', compact('payment'));
    }

    public function manualHistory() {
        $history = ManualReceipt::latest()->get();
        return view('admin.receipt.manual_history', compact('history'));
    }
}