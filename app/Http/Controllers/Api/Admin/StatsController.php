<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsAppMessageJob;
use App\Models\Complaint;
use App\Models\Finance;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function __construct(
        protected WhatsAppService $whatsapp,
    ) {}

    public function index(Request $request)
    {
        $now = now();
        $adminName = $request->user()?->name ?? 'Admin';

        // 1. Room Occupancy
        $total_rooms = Room::count();
        $occupied_rooms = Room::where('status', 'occupied')->count();
        $vacant_rooms = Room::where('status', 'available')->count();
        $vacant_rooms_list = Room::where('status', 'available')
            ->orderBy('room_number')
            ->take(6)
            ->get(['id', 'uuid', 'room_number', 'price', 'type']);

        // 2. Financials (Income, Expense, Net Profit)
        $monthly_income = (float) Payment::whereMonth('payment_date', $now->month)
            ->whereYear('payment_date', $now->year)
            ->whereIn('status', ['paid', 'Paid'])
            ->sum('amount');

        if ($monthly_income == 0) {
            $monthly_income = (float) Finance::income()->byMonthYear($now->month, $now->year)->sum('amount');
        }

        $monthly_expense = (float) Finance::expense()->byMonthYear($now->month, $now->year)->sum('amount');
        $net_profit = $monthly_income - $monthly_expense;

        // 3. Due & Overdue Tenants (Pengingat Jatuh Tempo)
        $dueTenantsQuery = Tenant::with(['room', 'payments'])
            ->where('status', 'active')
            ->get()
            ->filter(function ($tenant) {
                $dueDate = $tenant->calculated_due_date;
                if (! $dueDate) {
                    return false;
                }

                $isPaid = $tenant->payments->where('status', 'paid')
                    ->contains(function ($payment) use ($dueDate) {
                        return Carbon::parse($payment->period_month)->format('Y-m') === $dueDate->format('Y-m');
                    });

                return ! $isPaid && $tenant->days_left <= 7;
            })
            ->sortBy('days_left')
            ->values();

        $total_arrears = $dueTenantsQuery->sum(fn ($t) => (float) ($t->room->price ?? 0));
        $unpaid_tenants_count = $dueTenantsQuery->count();

        $due_tenants = $dueTenantsQuery->take(5)->map(function ($t) {
            $days = $t->days_left;
            $statusLabel = $days < 0
                ? 'Telat '.abs($days).' hari'
                : ($days == 0 ? 'Hari ini' : "{$days} hari lagi");

            return [
                'id' => $t->id,
                'uuid' => $t->uuid,
                'name' => $t->name,
                'room' => $t->room->room_number ?? '-',
                'phone' => $t->phone,
                'price' => (float) ($t->room->price ?? 0),
                'due_date' => $t->calculated_due_date ? Carbon::parse($t->calculated_due_date)->format('d M Y') : '-',
                'days_left' => $days,
                'status_label' => $statusLabel,
            ];
        })->values();

        // 4. Pending Actions & Active Complaints
        $pending_payments = Payment::whereIn('status', ['pending', 'waiting'])->count();
        $pending_complaints = Complaint::whereIn('status', ['open', 'in_progress', 'pending'])->count();

        $active_complaints_list = Complaint::with(['tenant', 'room'])
            ->whereIn('status', ['open', 'in_progress', 'pending'])
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'uuid' => $c->uuid,
                    'title' => $c->title,
                    'tenant_name' => $c->tenant->name ?? 'Penghuni',
                    'room' => $c->room->room_number ?? ($c->tenant->room->room_number ?? '-'),
                    'status' => $c->status,
                    'category' => $c->category ?? 'Umum',
                    'created_at' => $c->created_at ? $c->created_at->format('d M Y') : '-',
                ];
            });

        // 5. Payment History
        $payment_history = Payment::with('tenant.room')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($pay) {
                return [
                    'name' => $pay->tenant->name ?? 'N/A',
                    'room' => $pay->tenant->room->room_number ?? '-',
                    'amount' => (int) $pay->amount,
                    'date' => $pay->payment_date ? Carbon::parse($pay->payment_date)->format('d M Y') : '-',
                    'status' => ucfirst(strtolower($pay->status)),
                ];
            });

        return response()->json([
            'admin_name' => $adminName,
            'total_rooms' => $total_rooms,
            'occupied_rooms' => $occupied_rooms,
            'vacant_rooms' => $vacant_rooms,
            'vacant_rooms_list' => $vacant_rooms_list,
            'monthly_income' => (int) $monthly_income,
            'monthly_expense' => (int) $monthly_expense,
            'net_profit' => (int) $net_profit,
            'total_arrears' => (int) $total_arrears,
            'unpaid_tenants_count' => $unpaid_tenants_count,
            'due_tenants' => $due_tenants,
            'pending_payments' => $pending_payments,
            'pending_complaints' => $pending_complaints,
            'active_complaints_list' => $active_complaints_list,
            'payment_history' => $payment_history,
        ]);
    }

    public function sendReminder(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required',
        ]);

        try {
            $tenant = Tenant::with('room')->where('id', $request->tenant_id)
                ->orWhere('uuid', $request->tenant_id)
                ->firstOrFail();

            if (! $tenant->phone) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Nomor telepon penghuni {$tenant->name} belum terdaftar.",
                ], 422);
            }

            $daysLeft = $request->has('days_left') ? (int) $request->days_left : $tenant->days_left;
            $dueDate = $request->due_date ?? ($tenant->calculated_due_date ? Carbon::parse($tenant->calculated_due_date)->format('d M Y') : Carbon::now()->format('d M Y'));

            $message = $this->whatsapp->getReminderMessage(
                $tenant->name,
                $tenant->room->room_number ?? '-',
                $dueDate,
                (float) ($tenant->room->price ?? 0),
                $daysLeft
            );

            $cleanPhone = preg_replace('/[^0-9]/', '', $tenant->phone);
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62'.substr($cleanPhone, 1);
            }
            $waUrl = 'https://wa.me/'.$cleanPhone.'?text='.urlencode($message);

            $success = $this->whatsapp->sendMessage($tenant->phone, $message, 20);

            if ($success) {
                LogHelper::log(
                    'SEND_REMINDER',
                    "Mengirim reminder tagihan via Mobile ke {$tenant->name} (Kamar ".($tenant->room->room_number ?? '-').')'
                );

                return response()->json([
                    'status' => 'success',
                    'message' => "Pesan penagihan berhasil terkirim ke WhatsApp {$tenant->name}!",
                    'wa_url' => $waUrl,
                    'message_text' => $message,
                ]);
            }

            try {
                SendWhatsAppMessageJob::dispatch($tenant->phone, $message);
            } catch (\Throwable $e) {
                // Ignore queue dispatch error
            }

            LogHelper::logError('SEND_REMINDER_FAILED', "Gagal kirim reminder ke {$tenant->name} via gateway");

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal terhubung ke WhatsApp Gateway otomatis.',
                'wa_url' => $waUrl,
                'message_text' => $message,
            ], 502);

        } catch (\Exception $e) {
            LogHelper::logError('SEND_REMINDER_FAILED', 'Gagal kirim reminder tagihan', $e);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim reminder: '.$e->getMessage(),
            ], 500);
        }
    }
}
