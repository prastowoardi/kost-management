<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Finance;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function payments(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        $payments = Payment::with(['tenant', 'room'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date', 'desc')
            ->get();
        
        $totalPaid = Payment::where('status', 'paid')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('total');
        
        $totalPending = Payment::where('status', 'pending')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('total');
        
        return view('reports.payments', compact(
            'payments',
            'month',
            'year',
            'totalPaid',
            'totalPending'
        ));
    }
    
    public function finances(Request $request)
    {
        // Redirect to existing finance report
        return redirect()->route('finances.report', $request->all());
    }
}