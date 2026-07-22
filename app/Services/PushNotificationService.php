<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private string $expoUrl = 'https://exp.host/--/api/v2/push/send';

    public function send(string $token, string $title, string $body, array $data = []): bool
    {
        try {
            $response = Http::post($this->expoUrl, [
                'to' => $token,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'sound' => 'default',
            ]);

            Log::info('Expo Response: '.$response->body());

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Expo Error: '.$e->getMessage());

            return false;
        }
    }

    public function sendComplaintUpdate(string $token, string $title, string $status, ?string $response, int $complaintId): bool
    {
        return $this->send(
            $token,
            'Update Laporan: '.$title,
            'Status laporan Anda: '.strtoupper($status).'. '.($response ?? ''),
            [
                'type' => 'complaint_update',
                'id' => $complaintId,
            ]
        );
    }

    public function sendPaymentVerified(string $token, string $name, float $total, int $paymentId): bool
    {
        return $this->send(
            $token,
            'Pembayaran Diterima! ✅',
            'Pembayaran sebesar Rp '.number_format($total, 0, ',', '.').' telah diverifikasi.',
            [
                'type' => 'payment_verified',
                'id' => $paymentId,
            ]
        );
    }

    public function sendPaymentRejected(string $token, string $name, float $total, int $paymentId): bool
    {
        return $this->send(
            $token,
            'Pembayaran Ditolak! ❌',
            'Maaf, pembayaran Rp '.number_format($total, 0, ',', '.').' ditolak Admin. Silakan hubungi pengelola.',
            [
                'type' => 'payment_rejected',
                'id' => $paymentId,
            ]
        );
    }
}
