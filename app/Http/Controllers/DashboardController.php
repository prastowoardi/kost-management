<?php

namespace App\Http\Controllers;

use App\Models\{Room, Tenant, Payment, Complaint};
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Dasar
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $activeTenants = Tenant::where('status', 'active')->count();
        
        // 2. Statistik Pembayaran
        $paymentsThisMonth = Payment::whereYear('period_month', Carbon::now()->year)
                                    ->whereMonth('period_month', Carbon::now()->month)
                                    ->where('status', 'paid')
                                    ->sum('total');
        
        $pendingPayments = Payment::where('status', 'pending')->count();
        $overduePayments = Payment::where('status', 'overdue')->count();
        
        // 3. Keluhan
        $openComplaints = Complaint::where('status', 'open')->count();

        // 4. LOGIC JATUH TEMPO (H-7)
        // Ambil semua pembayaran yang belum lunas untuk dicek tanggalnya
        $duePayments = Tenant::with('room')
            ->where('status', 'active')
            ->get()
            ->filter(function ($tenant) {
                if (!$tenant->entry_date) return false;

                $entryDate = \Carbon\Carbon::parse($tenant->entry_date);
                $targetDate = \Carbon\Carbon::now()->setDay($entryDate->day);

                if (\Carbon\Carbon::now()->gt($targetDate->endOfDay())) {
                    $targetDate->addMonth();
                }

                // Hitung selisih hari dari SEKARANG ke TANGGAL JATUH TEMPO
                $tenant->days_left = (int) \Carbon\Carbon::now()->startOfDay()
                    ->diffInDays($targetDate->startOfDay(), false);
                
                $tenant->calculated_due_date = $targetDate;

                return $tenant->days_left <= 7 && $tenant->days_left >= 0;
            })->sortBy('days_left');;

        // 5. Aktivitas Terbaru
        $recentPayments = Payment::with(['tenant', 'room'])->latest()->take(5)->get();
        $recentComplaints = Complaint::with(['tenant', 'room'])->latest()->take(5)->get();

        // 6. Kirim ke View
        return view('dashboard', compact(
            'totalRooms',
            'occupiedRooms',
            'activeTenants',
            'paymentsThisMonth',
            'pendingPayments',
            'overduePayments',
            'openComplaints',
            'duePayments',
            'recentPayments',
            'recentComplaints',
            'duePayments'
        ));
    }
}