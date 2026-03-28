<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Payment;
use App\Models\Complaint;

class MobileTenantController extends Controller
{
    public function getDashboardData(Request $request)
    {
        $user = $request->user();
        // Ambil data tenant beserta info kamarnya
        $tenant = \App\Models\Tenant::with('room')->where('user_id', $user->id)->first();
        
        // Ambil tagihan terbaru yang belum lunas
        $latestPayment = \App\Models\Payment::where('tenant_id', $tenant->id)
                            ->where('status', 'pending')
                            ->first();

        return response()->json([
            'status' => 'success',
            'user_name' => $user->name,
            'room_number' => $tenant->room->name ?? 'Belum ada kamar',
            'floor' => $tenant->room->floor ?? '-',
            'bill_amount' => $latestPayment ? $latestPayment->amount : 0,
        ]);
    }

    public function getProfile(Request $request)
    {
        $user = $request->user();
        $tenant = \App\Models\Tenant::with('room')->where('user_id', $user->id)->first();

        if (!$tenant) {
            return response()->json(['name' => $user->name, 'payment_status' => 'Belum Terdaftar'], 404);
        }

        $latestPayment = \App\Models\Payment::where('tenant_id', $tenant->id)
                            ->latest()
                            ->first();

        $paymentStatus = 'Belum Bayar';
        $displayDueDate = $tenant->calculated_due_date;

        if ($latestPayment) {
            $dbStatus = strtolower($latestPayment->status);
            
            if (in_array($dbStatus, ['paid', 'lunas', 'verified', 'success'])) {
                $paymentStatus = 'Lunas';
                
                $originalDay = \Carbon\Carbon::parse($tenant->calculated_due_date)->day;

                if ($latestPayment->period_month) {
                    $displayDueDate = \Carbon\Carbon::parse($latestPayment->period_month)
                                        ->addMonth()
                                        ->day($originalDay);
                }
            }
        }

        $now = now()->startOfDay();
        $dueDateObj = \Carbon\Carbon::parse($displayDueDate)->startOfDay();
        $diff = $now->diffInDays($dueDateObj, false);

        $lateStatus = "";
        if ($paymentStatus === 'Lunas') {
            $namaBulan = \Carbon\Carbon::parse($latestPayment->period_month)->translatedFormat('F Y');
            
            if ($diff <= 0) {
                $paymentStatus = 'Belum Bayar'; // Otomatis jadi MERAH
                $lateStatus = ($diff == 0) 
                    ? "HARI INI JATUH TEMPO, SILAKAN MELAKUKAN PEMBAYARAN" 
                    : "LEWAT " . abs($diff) . " HARI DARI TANGGAL JATUH TEMPO";
            } else if ($diff <= 7) {
                $lateStatus = "$diff HARI HINGGA JATUH TEMPO SELANJUTNYA";
            }
        } else {
            $lateStatus = ($diff < 0) 
                ? 'LEWAT ' . abs($diff) . ' HARI DARI JATUH TEMPO' 
                : 'AKTIF';
        }

        return response()->json([
            'email'          => $user->email,
            'phone'          => $tenant->phone,
            'address'        => $tenant->address,
            'id_card'        => $tenant->id_card,
            'name'           => $user->name,
            'room_number'    => $tenant->room->room_number ?? '-',
            'due_date'       => $displayDueDate ? \Carbon\Carbon::parse($displayDueDate)->format('d M Y') : '-',
            'days_left_msg'  => $lateStatus,
            'payment_status' => $paymentStatus,
            'bill_amount'    => $tenant->room->price ?? 0,
        ]);
    }

    public function getPayments(Request $request)
    {
        $payments = Payment::where('tenant_id', function($query) use ($request) {
            $query->select('id')->from('tenants')->where('user_id', $request->user()->id);
        })->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $payments
        ]);
    }

    public function storeComplaint(Request $request)
    {
        $request->validate([
            'description' => 'required',
        ]);

        $complaint = Complaint::create([
            'user_id' => $request->user()->id,
            'description' => $request->description,
            'status' => 'pending'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Keluhan berhasil dikirim',
            'data' => $complaint
        ]);
    }
}