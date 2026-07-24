<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Complaint;
use App\Models\Finance;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private WhatsAppService $whatsapp,
    ) {}

    public function index()
    {
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $activeTenants = Tenant::where('status', 'active')->count();

        $paymentsThisMonth = Finance::income()
            ->whereIn('category', ['Pembayaran Sewa', 'Deposit'])
            ->whereYear('transaction_date', Carbon::now()->year)
            ->whereMonth('transaction_date', Carbon::now()->month)
            ->sum('amount');

        $allDueTenants = Tenant::with(['room', 'payments'])
            ->where('status', 'active')
            ->get()
            ->filter(function ($tenant) {
                if (! $tenant->entry_date) {
                    return false;
                }

                $now = Carbon::now()->startOfDay();
                $entryDate = Carbon::parse($tenant->entry_date)->startOfDay();

                if ($entryDate->greaterThan($now)) {
                    return false;
                }

                $targetDate = Carbon::now()->setDay($entryDate->day)->startOfDay();

                $diff = $now->diffInDays($targetDate, false);
                if ($diff < -20) {
                    $targetDate->addMonth();
                } elseif ($diff > 20) {
                    $targetDate->subMonth();
                }

                $isPaid = $tenant->payments->where('status', 'paid')
                    ->filter(function ($payment) use ($targetDate) {
                        return Carbon::parse($payment->period_month)->format('Y-m') === $targetDate->format('Y-m');
                    })->first();

                $tenant->days_left = (int) $now->diffInDays($targetDate, false);
                $tenant->calculated_due_date = $targetDate;

                return ! $isPaid && ($tenant->days_left <= 7 && $tenant->days_left >= -14);
            });

        $duePayments = $allDueTenants->sortBy('days_left');
        $pendingPayments = $allDueTenants->count();

        $overduePayments = Payment::where('status', 'overdue')->count();
        $openComplaints = Complaint::where('status', 'open')->count();

        $recentPayments = Finance::income()
            ->whereIn('category', ['Pembayaran Sewa', 'Deposit'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

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
            $message = $this->whatsapp->getReminderMessage(
                $tenant->name,
                $tenant->room->room_number,
                $request->due_date,
                $tenant->room->price,
                $daysLeft
            );

            $success = $this->whatsapp->sendMessage($tenant->phone, $message, 20);

            if ($success) {
                return response()->json(['status' => 'success', 'message' => 'Pesan penagihan berhasil terkirim!']);
            }

            return response()->json(['status' => 'error', 'message' => 'Gagal terhubung ke WhatsApp Gateway.'], 500);

        } catch (\Exception $e) {
            LogHelper::logError('SEND_REMINDER_FAILED', 'Gagal kirim reminder tagihan', $e);

            return response()->json(['status' => 'error', 'message' => 'Gagal mengirim reminder'], 500);
        }
    }
}
