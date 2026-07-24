<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TenantRegistrationService
{
    public function registerWithUser(array $tenantData, ?string $password = null): Tenant
    {
        return DB::transaction(function () use ($tenantData, $password) {
            $user = User::create([
                'name' => $tenantData['name'],
                'email' => $tenantData['email'],
                'password' => $password ?? 'password123',
                'role' => 'tenant',
            ]);

            $tenantData['user_id'] = $user->id;

            $tenant = Tenant::create($tenantData);
            $tenant->load('room');

            $tenant->room->update(['status' => 'occupied']);

            return $tenant;
        });
    }

    public function registerWithPayment(array $tenantData, array $paymentData, ?string $password = null): Tenant
    {
        return DB::transaction(function () use ($tenantData, $paymentData, $password) {
            $tenant = $this->registerWithUser($tenantData, $password);

            Payment::create(array_merge([
                'tenant_id' => $tenant->id,
                'room_id' => $tenant->room_id,
                'amount' => $tenant->room->price,
                'late_fee' => 0,
                'total' => $tenant->room->price,
                'payment_date' => now(),
                'status' => 'pending',
                'notes' => 'Pembayaran pendaftaran awal dari form publik',
                'period_month' => date('Y-m-01'),
            ], $paymentData));

            return $tenant;
        });
    }
}
