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

    public function manualStore(Request $request)
    {
        $request->validate([
            'tenant_name'    => 'required|string|max:255',
            'room_number'    => 'required|string',
            'period'         => 'required|string',
            'total_amount'   => 'required|numeric',
            'invoice_number' => 'required|string|unique:manual_receipts,invoice_number',
        ]);

        $receipt = new \App\Models\ManualReceipt();
        $receipt->invoice_number = $request->invoice_number;
        $receipt->tenant_name    = $request->tenant_name;
        $receipt->room_number    = $request->room_number;
        $receipt->period         = $request->period;
        $receipt->total_amount   = $request->total_amount;
        $receipt->save();

        return redirect()->route('admin.receipt.print', $receipt->id)
                            ->with('success', 'Kwitansi berhasil dibuat!');
    }

    public function manualPrint($id)
    {
        $payment = \App\Models\ManualReceipt::findOrFail($id);
        return view('admin.receipt.manual_print', compact('payment'));
    }

    public function manualHistory()
    {
        $receipts = \App\Models\ManualReceipt::orderBy('created_at', 'desc')->get();
        return view('admin.receipt.manual_history', compact('receipts'));
    }
}