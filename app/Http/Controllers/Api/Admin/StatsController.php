<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Payment;
use App\Models\Complaint;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        $total_rooms = Room::count();
        $occupied_rooms = Room::where('status', 'occupied')->count();
        $vacant_rooms = Room::where('status', 'available')->count();

        $monthly_income = Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->whereIn('status', ['paid', 'Paid'])
            ->sum('amount');

        $monthly_reports = [];
        for ($i = 4; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $amount = Payment::whereMonth('payment_date', $month->month)
                ->whereYear('payment_date', $month->year)
                ->whereIn('status', ['paid', 'Paid'])
                ->sum('amount');

            $monthly_reports[] = [
                'month' => $month->format('M'),
                'amount' => (int)$amount,
            ];
        }

        $payment_history = Payment::with('tenant.room')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($pay) {
                return [
                    'name' => $pay->tenant->name ?? 'N/A',
                    'room' => $pay->tenant->room->room_number ?? '-',
                    'amount' => (int)$pay->amount,
                    'date' => $pay->payment_date->format('d M Y'),
                    'status' => ucfirst(strtolower($pay->status)),
                ];
            });

        return response()->json([
            'total_rooms' => $total_rooms,
            'occupied_rooms' => $occupied_rooms,
            'vacant_rooms' => $vacant_rooms,
            'monthly_income' => (int)$monthly_income,
            'monthly_reports' => $monthly_reports,
            'payment_history' => $payment_history,
        ]);
    }
}