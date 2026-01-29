<?php

namespace App\Http\Controllers;

use App\Models\{Room, Tenant, Payment, Complaint};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Dasar
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $activeTenants = Tenant::where('status', 'active')->count();
        
        // 2. Statistik Pembayaran (Lunas Bulan Ini)
        $paymentsThisMonth = Payment::whereYear('period_month', Carbon::now()->year)
                                    ->whereMonth('period_month', Carbon::now()->month)
                                    ->where('status', 'paid')
                                    ->sum('total');
        
        // 3. LOGIKA UTAMA: JATUH TEMPO & PENDING (Satu Kali Proses)
        $allDueTenants = Tenant::with(['room', 'payments'])
            ->where('status', 'active')
            ->get()
            ->filter(function ($tenant) {
                if (!$tenant->entry_date) return false;
                
                $now = Carbon::now()->startOfDay();
                $entryDate = Carbon::parse($tenant->entry_date);
                $targetDate = Carbon::now()->setDay($entryDate->day)->startOfDay();

                $diff = $now->diffInDays($targetDate, false);
                if ($diff < -20) { $targetDate->addMonth(); } 
                elseif ($diff > 20) { $targetDate->subMonth(); }

                $isPaid = $tenant->payments->where('status', 'paid')
                    ->filter(function ($payment) use ($targetDate) {
                        return Carbon::parse($payment->period_month)->format('Y-m') === $targetDate->format('Y-m');
                    })->first();

                $tenant->days_left = (int) $now->diffInDays($targetDate, false);
                $tenant->calculated_due_date = $targetDate;

                return !$isPaid && ($tenant->days_left <= 7 && $tenant->days_left >= -14);
            });

        $duePayments = $allDueTenants->sortBy('days_left');
        $pendingPayments = $allDueTenants->count();

        $overduePayments = Payment::where('status', 'overdue')->count();
        $openComplaints = Complaint::where('status', 'open')->count();

        $recentPayments = Payment::with(['tenant', 'room'])->latest()->take(5)->get();
        $recentComplaints = Complaint::with(['tenant', 'room'])->latest()->take(5)->get();

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
            'recentComplaints'
        ));
    }

    public function sendReminder(Request $request)
    {
        try {
            $tenant = Tenant::with('room')->findOrFail($request->tenant_id);
            $daysLeft = (int) $request->days_left;

            if ($daysLeft < 0) {
                // Pesan untuk yang telat
                $daysLate = abs($daysLeft);
                $message = "Yth. *" . $tenant->name . "*,\n\n" .
                        "Mohon maaf mengganggu. Kami menginformasikan bahwa tagihan sewa Kamar *" . $tenant->room->room_number . "* telah *MELEWATI JATUH TEMPO* selama *" . $daysLate . " hari* (Tgl: " . $request->due_date . ").\n\n" .
                        "Total Tagihan: *Rp " . number_format($tenant->room->price, 0, ',', '.') . "*\n\n" .
                        "Mohon segera melakukan pembayaran agar tidak terkena denda atau pemutusan fasilitas. Jika sudah membayar, abaikan pesan ini.\n\n" .
                        "Terima kasih.";
            } else {
                // Pesan untuk pengingat
                $statusTeks = ($daysLeft == 0) ? "HARI INI" : "dalam " . $daysLeft . " hari lagi";
                $message = "Halo *" . $tenant->name . "*,\n\n" .
                        "Sekadar mengingatkan bahwa sewa Kamar *" . $tenant->room->room_number . "* akan jatuh tempo *" . $statusTeks . "* (" . $request->due_date . ").\n\n" .
                        "Nilai Tagihan: *Rp " . number_format($tenant->room->price, 0, ',', '.') . "*\n\n" .
                        "Terima kasih atas kerjasamanya.";
            }

            $response = Http::timeout(20)->post('http://127.0.0.1:3000/send-message', [
                'number'  => $tenant->phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return response()->json(['status' => 'success', 'message' => 'Pesan penagihan berhasil terkirim!']);
            }
            
            return response()->json(['status' => 'error', 'message' => 'Gagal terhubung ke WhatsApp Gateway.'], 500);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}