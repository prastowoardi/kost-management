<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MobileDashboardController extends Controller
{
    public function index(Request $request) 
    {
        $user = $request->user();
        $tenant = \App\Models\Tenant::where('user_id', $user->id)->with('room')->first();

        $currentPayment = \App\Models\Payment::where('tenant_id', $tenant->id)
            ->whereMonth('period_month', now()->month)
            ->whereYear('period_month', now()->year)
            ->first();

        $isPaid = $currentPayment && $currentPayment->status == 'success';
        $isPending = $currentPayment && $currentPayment->status == 'pending';

        return response()->json([
            'user_name' => $user->name,
            'room_number' => $tenant->room->room_number ?? '-',
            'bill_amount' => $tenant->room->price ?? 0,
            'payment_status' => $isPaid ? 'Lunas' : ($isPending ? 'Pending' : 'Belum Bayar'),
            'due_date' => '10 ' . now()->format('M Y'),
        ]);
    }
}
