<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function payments(Request $request)
    {
        $query = Payment::with(['tenant', 'room'])->orderBy('payment_date', 'desc');

        if ($request->filled('start_date')) {
            $query->whereDate('payment_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('payment_date', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->get();

        $totalAmount = (float) $payments->sum('total');
        $totalPaid = (float) $payments->where('status', 'paid')->sum('total');
        $totalPending = (float) $payments->where('status', 'pending')->sum('total');

        if ($request->query('download') === 'pdf') {
            $pdf = Pdf::loadView('payments.report_pdf', compact('payments', 'totalAmount', 'totalPaid', 'totalPending'));

            return $pdf->download('laporan-pembayaran-'.now()->format('Y-m-d').'.pdf');
        }

        return view('payments.report', compact(
            'payments',
            'totalAmount',
            'totalPaid',
            'totalPending'
        ));
    }

    public function finances(Request $request)
    {
        return redirect()->route('finances.report', $request->all());
    }
}
