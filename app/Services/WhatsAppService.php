<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send WhatsApp message with receipt
     */
    public function sendReceipt($phoneNumber, $payment)
    {
        // Format phone number
        $phone = $this->formatPhoneNumber($phoneNumber);
        
        // Generate message
        $message = $this->generateReceiptMessage($payment);
        
        try {
            // Simple WhatsApp Web Link (No API needed)
            return $this->sendViaWAWeb($phone, $message);
        } catch (\Exception $e) {
            Log::error('WhatsApp send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send via WhatsApp Web (Simple - No API needed)
     */
    private function sendViaWAWeb($phone, $message)
    {
        $url = "https://wa.me/$phone?text=" . urlencode($message);
        return $url;
    }

    /**
     * Format phone number (Indonesia)
     */
    public function formatPhoneNumber($phone)
    {
        // Remove all non-numeric
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 0, replace with 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // If doesn't start with 62, add it
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    /**
     * Generate receipt message
     */
    public function generateReceiptMessage($payment)
    {
        $message = "🏠 *KOS MANAGEMENT*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
        $message .= "📄 *KWITANSI PEMBAYARAN*\n\n";
        $message .= "Invoice: *{$payment->invoice_number}*\n";
        $message .= "Penghuni: {$payment->tenant->name}\n";
        $message .= "Kamar: {$payment->room->room_number}\n";
        $message .= "Periode: {$payment->period_month->format('F Y')}\n";
        $message .= "Tanggal: {$payment->payment_date->format('d F Y')}\n\n";
        $message .= "💰 *Total: Rp " . number_format($payment->total, 0, ',', '.') . "*\n";
        $message .= "Status: " . ($payment->status == 'paid' ? '✅ LUNAS' : '⏳ Pending') . "\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "Terima kasih atas pembayaran Anda!\n\n";
        $message .= "Lihat kwitansi lengkap:\n";
        $message .= route('payments.receipt', $payment);
        
        return $message;
    }
}