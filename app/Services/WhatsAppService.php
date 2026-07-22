<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $gatewayUrl;

    public function __construct()
    {
        $this->gatewayUrl = config('services.whatsapp.gateway_url', 'http://localhost:3000');
    }

    public function sendMessage(string $phone, string $message, int $timeout = 10): bool
    {
        try {
            $response = Http::timeout($timeout)->post("{$this->gatewayUrl}/send-message", [
                'number' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("WA Terkirim ke {$phone}");

                return true;
            }

            Log::error("WA Gateway Error ke {$phone}: ".$response->body());

            return false;
        } catch (\Exception $e) {
            Log::error('Gagal koneksi ke WA Gateway: '.$e->getMessage());

            return false;
        }
    }

    public function sendImage(string $phone, string $html, string $caption, int $timeout = 60): bool
    {
        try {
            $response = Http::timeout($timeout)->post("{$this->gatewayUrl}/send-image", [
                'number' => $phone,
                'html' => $html,
                'message' => $caption,
            ]);

            if ($response->successful()) {
                Log::info("WA Image terkirim ke {$phone}");

                return true;
            }

            Log::error("WA Image Gateway Error ke {$phone}: ".$response->body());

            return false;
        } catch (\Exception $e) {
            Log::error('Gagal koneksi ke WA Gateway (image): '.$e->getMessage());

            return false;
        }
    }

    public function getChats(string $phone, int $timeout = 10): array
    {
        try {
            $response = Http::timeout($timeout)->get("{$this->gatewayUrl}/get-chats", [
                'number' => $phone,
            ]);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Gagal ambil chat dari WA Gateway: '.$e->getMessage());

            return [];
        }
    }

    public function getWelcomeMessage(string $name): string
    {
        return "Halo Kak {$name}! Selamat datang di Serrata Kost! 👋✨\n\n".

                "Makasih banyak ya sudah memilih Serrata Kost jadi rumah barumu. Semoga betah, nyaman, dan produktif selama tinggal di sini! 😊\n\n".

                "*Biar makin nyaman bareng, yuk intip 'House Rules' kita sebentar:* 📝\n\n".

                "📍 *UMUM*\n".
                    "1. Saling jaga ketenangan dan keamanan ya, biar istirahat makin pol.\n".
                    "2. Kost kita bersih dari Miras, Narkoba, atau barang terlarang lainnya.\n".
                    "3. No smoking inside! Kamar tetap wangi tanpa asap rokok ya.\n".
                    "4. Mohon maaf, kita belum bisa terima anabul atau hewan peliharaan.\n".
                    "5. Saling jaga etika dan hindari asusila demi kenyamanan bersama.\n".
                    "6. Kita jaga nama baik Serrata Kost bareng-bareng ya, Kak.\n".
                    "7. Jangan lupa pembayaran kost tepat waktu sesuai tanggal janjian.\n".
                    "8. Khusus tamu laki-laki tidak boleh masuk kamar.\n".
                    "9. Kalau ada keluarga mau menginap, info ke Ibu Kost dulu ya (max 2 orang).\n\n".

                "✨ *KEBERSIHAN & KERAPIHAN*\n".
                    "1. Kamar dan kamar mandi sendiri dijaga tetap bersih ya, biar makin betah.\n".
                    "2. Buang sampah di tempatnya. Tolong banget jangan buang sampah/pembalut di kloset biar nggak mampet.\n".
                    "3. Jemur pakaian di tempat jemuran yang sudah ada ya, jangan di depan kamar agar tetap rapi.\n\n".

                "🚰 *FASILITAS*\n".
                    "1. Gunakan fasilitas kost dengan bijak dan penuh rasa tanggung jawab.\n".
                    "2. Jika ada kerusakan karena kelalaian, biaya perbaikannya ditanggung penghuni dulu ya.\n".
                    "3. Hemat air ya Kak, gunakan secukupnya saja sesuai kebutuhan.\n\n".

                'Sekali lagi, selamat bergabung di keluarga besar Serrata Kost! Kalau ada apa-apa, jangan sungkan hubungi kami ya. Enjoy your stay! 🏠💖';
    }

    public function getRegistrationMessages(string $name, string $roomNumber, string $entryDate, float $price): array
    {
        $message1 = "Halo {$name}! Pendaftaran kamu di Serrata Kost BERHASIL. 👋✨\n\n".
                    "Kamar: *{$roomNumber}*\n".
                    "Tgl Masuk: {$entryDate}\n\n".
                    'Tunggu sebentar ya, mimin kirimkan tata tertib kost di bawah ini. 👇';

        $message2 = "Ini dia *'Rules of the House'* di Serrata Kost: 📝\n\n".
                    "📍 UMUM\n".
                    "1. Jaga ketenangan, kebersihan, dan keamanan lingkungan ya.\n".
                    "2. Kost kita bebas dari Miras, Narkoba, dan barang terlarang lainnya.\n".
                    "3. No smoking inside (dilarang merokok di kamar).\n".
                    "4. Tidak diperbolehkan membawa anabul/hewan peliharaan.\n".
                    "5. Jaga perilaku asusila demi kenyamanan bersama.\n".
                    "6. Jaga nama baik Serrata Kost di mana pun kita berada.\n".
                    "7. Pembayaran kost tepat waktu ya Kak, sesuai tanggal janjian.\n\n".
                "⏰ JAM KEGIATAN\n".
                    "1. Jam malam maksimal pukul 22.00 WIB. Kalau terpaksa telat, wajib kabari Ibu Kost ya.\n".
                    "2. Tamu laki-laki dilarang masuk kamar (ketemu di teras saja).\n".
                    "3. Ada keluarga mau menginap? Wajib lapor dulu ke Ibu Kost ya (Max. 2 orang).\n\n".
                "✨ KEBERSIHAN\n".
                    "1. Jaga kebersihan kamar & kamar mandi masing-masing.\n".
                    "2. Buang sampah pada tempatnya (jangan buang sampah/pembalut di kloset ya, be gentle with the toilet!).\n".
                    "3. Jemur pakaian di tempat jemuran yang tersedia, jangan di depan kamar ya Kak.\n\n".
                "🚰 FASILITAS\n".
                    "1. Gunakan fasilitas kost dengan bijak & hemat air.\n".
                    "2. Jika ada kerusakan karena kelalaian, biaya perbaikan ditanggung penghuni ya.\n\n".

                    'Sekali lagi, selamat bergabung! Selamat istirahat dan semoga betah di Serrata Kost! 🏠💖';

        return [$message1, $message2];
    }

    public function getReminderMessage(string $name, string $roomNumber, string $dueDate, float $price, int $daysLeft): string
    {
        if ($daysLeft < 0) {
            $daysLate = abs($daysLeft);

            return "Halo kak *{$name}*,\n\n".
                    "Mohon maaf mengganggu. Kami menginformasikan bahwa tagihan sewa Kamar *{$roomNumber}* telah *MELEWATI JATUH TEMPO* selama *{$daysLate} hari* (Tgl: {$dueDate}).\n\n".
                    'Total Tagihan: *Rp '.number_format($price, 0, ',', '.')."*\n\n".
                    "Mohon segera melakukan pembayaran agar tidak terkena denda atau pemutusan fasilitas. Jika sudah membayar, abaikan pesan ini.\n\n".
                    'Terima kasih.';
        }

        $statusTeks = ($daysLeft == 0) ? 'HARI INI' : "dalam {$daysLeft} hari lagi";

        return "Halo kak *{$name}*,\n\n".
                "Sekadar mengingatkan bahwa sewa Kamar *{$roomNumber}* akan jatuh tempo *{$statusTeks}* ({$dueDate}).\n\n".
                'Nilai Tagihan: *Rp '.number_format($price, 0, ',', '.')."*\n\n".
                "Mohon segera melakukan pembayaran agar tidak terkena denda atau pemutusan fasilitas. Jika sudah membayar, abaikan pesan ini.\n\n".
                'Terima kasih';
    }

    public function getPaymentReceiptCaption(string $name): string
    {
        return "Halo Kak *{$name}*, berikut adalah kwitansi pembayaran Anda.";
    }

    public function getPaymentConfirmationMessage(string $name, string $period, string $invoice, float $total): string
    {
        $totalFormatted = number_format($total, 0, ',', '.');

        return "Halo kak {$name},\n\n".
                "Pembayaran kos periode {$period} telah kami terima.\n\n".
                "Detail:\n".
                "* No. Invoice: {$invoice}\n".
                "* Total: Rp {$totalFormatted}\n\n".
                'Terima kasih.';
    }
}
