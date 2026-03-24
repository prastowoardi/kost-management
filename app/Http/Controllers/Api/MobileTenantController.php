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
            'bill_amount' => $latestPayment->amount ?? 0,
        ]);
}

    public function getProfile(Request $request)
    {
        $user = $request->user();
        $tenant = \App\Models\Tenant::with('room')
                        ->where('user_id', $user->id)
                        ->first();

        if (!$tenant) {
            return response()->json([
                'id'      => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
                'role'    => $user->role,
                'phone'   => 'Belum Terdaftar',
                'id_card' => '-',
                'address' => 'Data Tenant Tidak Ditemukan di Database',
                'room_number' => '-'
            ]);
        }

        return response()->json([
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $user->role,
            'phone'      => $tenant->phone,
            'id_card'    => $tenant->id_card,
            'address'    => $tenant->address,
            'room_number'  => $tenant->room ? $tenant->room->room_number : 'Belum diatur',
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