<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Tenant;
use App\Models\Payment;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total statistik
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $availableRooms = Room::where('status', 'available')->count();
        $activeTenants = Tenant::where('status', 'active')->count();
        
        // Pembayaran bulan ini
        $currentMonth = now()->format('Y-m');
        $paymentsThisMonth = Payment::whereYear('period_month', now()->year)
                                    ->whereMonth('period_month', now()->month)
                                    ->sum('total');
        
        $pendingPayments = Payment::where('status', 'pending')->count();
        $overduePayments = Payment::where('status', 'overdue')->count();
        
        // Keluhan terbuka
        $openComplaints = Complaint::where('status', 'open')->count();
        
        // Pembayaran terbaru
        $recentPayments = Payment::with(['tenant', 'room'])
                                    ->orderBy('created_at', 'desc')
                                    ->take(5)
                                    ->get();
        
        // Keluhan terbaru
        $recentComplaints = Complaint::with(['tenant', 'room'])
                                        ->orderBy('created_at', 'desc')
                                        ->take(5)
                                        ->get();
        
        // Chart data - Pembayaran 6 bulan terakhir
        $paymentChart = Payment::select(
                            DB::raw('DATE_FORMAT(period_month, "%Y-%m") as month'),
                            DB::raw('SUM(total) as total')
                        )
                        ->where('period_month', '>=', now()->subMonths(6))
                        ->groupBy('month')
                        ->orderBy('month')
                        ->get();
        
        return view('dashboard', compact(
            'totalRooms',
            'occupiedRooms',
            'availableRooms',
            'activeTenants',
            'paymentsThisMonth',
            'pendingPayments',
            'overduePayments',
            'openComplaints',
            'recentPayments',
            'recentComplaints',
            'paymentChart'
        ));
    }
}