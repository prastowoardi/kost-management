<?php

namespace App\Services;

use App\Helpers\LogHelper;
use App\Models\Complaint;
use App\Models\Finance;
use App\Models\Payment;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class WhatsAppBotService
{
    public function __construct(private WhatsAppService $whatsApp) {}

    public function handle(array $payload): void
    {
        $phone = $this->normalizeNumber((string) ($payload['phone'] ?? ''));
        $name = trim((string) ($payload['name'] ?? ''));
        $text = trim((string) ($payload['text'] ?? ''));
        $media = $payload['media'] ?? null;

        if (! $phone || strlen($phone) < 9) {
            return;
        }

        if (is_array($media) && ! empty($media['data'])) {
            $reply = $this->registerPaymentProof($phone, $media);
        } else {
            $reply = $this->routeCommand($phone, $name, $text);
        }

        if ($reply !== null) {
            $this->whatsApp->sendMessage($phone, $reply);
        }
    }

    private function routeCommand(string $phone, string $name, string $text): ?string
    {
        $command = $this->firstWord($text);

        return match ($command) {
            'MENU', 'BANTUAN', 'HELP', 'PANDUAN' => $this->helpText(),
            'LAPOR' => $this->registerComplaint($phone, $name, $text),
            'FINANCE', 'KEUANGAN' => $this->registerFinance($phone, $text),
            default => $text === '' ? $this->helpText() : $this->unknownText(),
        };
    }

    private function registerPaymentProof(string $phone, array $media): ?string
    {
        $tenants = $this->findTenants($phone);

        if ($tenants->isEmpty()) {
            return "Halo, sepertinya nomor *{$phone}* belum terdaftar sebagai penghuni Serrata Kost. Silakan hubungi Ibu Kost untuk informasi lebih lanjut.";
        }

        try {
            $ext = $this->extensionFromMime((string) ($media['mimetype'] ?? 'image/jpeg'));
            $filename = 'wa-'.now()->format('YmdHis').'-'.Str::lower(Str::random(6)).'.'.$ext;

            if (Storage::put("receipts/{$filename}", base64_decode($media['data'], true)) === false) {
                throw new \RuntimeException('Gagal menyimpan gambar bukti pembayaran.');
            }

            $receiptPath = "receipts/{$filename}";
            $details = [];

            foreach ($tenants as $tenant) {
                $price = (float) ($tenant->room->price ?? 0);
                $period = $this->tenantDuePeriod($tenant);

                $payment = Payment::create([
                    'tenant_id' => $tenant->id,
                    'room_id' => $tenant->room_id,
                    'payment_date' => now()->toDateString(),
                    'period_month' => $period->toDateString(),
                    'amount' => $price,
                    'late_fee' => 0,
                    'total' => $price,
                    'status' => 'pending',
                    'payment_method' => 'transfer',
                    'receipt_file' => $receiptPath,
                    'notes' => 'Pembayaran via WhatsApp Bot',
                ]);

                LogHelper::log(
                    'UPLOAD_RECEIPT_WA',
                    'Bukti bayar via WA diterima: Rp'.number_format($price, 0, ',', '.').' untuk periode '.$period->translatedFormat('F Y'),
                    $payment,
                    ['phone' => $phone]
                );

                $details[] = '- *'.$this->tenantName($tenant).'*: '.$payment->invoice_number.
                    ' — Rp '.number_format($price, 0, ',', '.').
                    ' ('.$period->translatedFormat('F Y').')';
            }

            return "Halo Kak! 🎉\n\n".
                "Bukti pembayaran kamu sudah kami terima.\n\n".
                implode("\n", $details)."\n\n".
                'Mohon tunggu konfirmasi dari Ibu Kost ya. Terima kasih! 🙏';
        } catch (Throwable $e) {
            LogHelper::logError('UPLOAD_RECEIPT_WA_FAILED', 'Gagal memproses bukti bayar via WA', $e, ['phone' => $phone]);

            return 'Maaf, kami gagal memproses bukti pembayaran kamu. Silakan coba lagi atau hubungi Ibu Kost ya.';
        }
    }

    private function registerComplaint(string $phone, string $name, string $text): ?string
    {
        $tenants = $this->findTenants($phone);

        if ($tenants->isEmpty()) {
            return "Halo, sepertinya nomor *{$phone}* belum terdaftar sebagai penghuni Serrata Kost. Silakan hubungi Ibu Kost untuk informasi lebih lanjut.";
        }

        $body = trim(substr($text, strlen($this->firstWord($text))));

        if ($body === '') {
            return "Format laporan masih kosong. Contoh:\n*LAPOR* fasilitas: AC kamar 5 bocor\n\nKategori opsional: facility, cleanliness, security, other.";
        }

        $category = 'other';

        if (preg_match('/^\s*(facility|cleanliness|security|other|fasilitas|kebersihan|keamanan|lainnya)\s*[:\-–]?\s*(.+)$/i', $body, $m)) {
            $map = [
                'fasilitas' => 'facility',
                'kebersihan' => 'cleanliness',
                'keamanan' => 'security',
                'lainnya' => 'other',
            ];

            $category = $map[strtolower($m[1])] ?? strtolower($m[1]);
            $body = trim($m[2]);
        }

        try {
            $title = Str::limit($body, 60, '');

            foreach ($tenants as $tenant) {
                $complaint = Complaint::create([
                    'tenant_id' => $tenant->id,
                    'room_id' => $tenant->room_id,
                    'title' => $title,
                    'description' => $body,
                    'category' => $category,
                    'priority' => 'medium',
                    'status' => 'open',
                ]);

                LogHelper::log('CREATE_COMPLAINT_WA', "Laporan via WA: {$title}", $complaint, ['phone' => $phone]);
            }

            return "Laporan kamu sudah kami terima ya! 🙏\n\n".
                "- *Judul:* {$title}\n\n".
                'Tim kami akan segera menindaklanjuti. Terima kasih!';
        } catch (Throwable $e) {
            LogHelper::logError('CREATE_COMPLAINT_WA_FAILED', 'Gagal membuat laporan via WA', $e, ['phone' => $phone]);

            return 'Maaf, kami gagal menyimpan laporan kamu. Silakan coba lagi atau hubungi Ibu Kost.';
        }
    }

    private function registerFinance(string $phone, string $text): ?string
    {
        if (! $this->isAdmin($phone)) {
            return 'Perintah keuangan hanya bisa digunakan oleh *admin/Ibu Kost*. Untuk info lain ketik *MENU*.';
        }

        $parts = preg_split('/\s+/', trim($text));
        array_shift($parts); // buang kata perintah (FINANCE/KEUANGAN)

        if (count($parts) < 3) {
            return "Format salah.\nContoh: *FINANCE expense 50000 Listrik Token bulan ini*\n\nTipe: income/expense atau masuk/keluar";
        }

        $typeRaw = strtolower($parts[0]);
        $type = match ($typeRaw) {
            'income', 'pemasukan', 'masuk' => 'income',
            'expense', 'pengeluaran', 'keluar' => 'expense',
            default => null,
        };

        if (! $type) {
            return "Tipe tidak dikenali ({$parts[0]}). Gunakan income/expense atau masuk/keluar.";
        }

        $amount = $this->parseAmount($parts[1]);

        if ($amount === null || $amount < 0) {
            return "Jumlah tidak valid: {$parts[1]}.\nContoh: 50000, 50.000, 50,000";
        }

        $category = $parts[2];
        $description = trim(implode(' ', array_slice($parts, 3)));
        if ($description === '') {
            $description = $category;
        }

        try {
            $finance = Finance::create([
                'type' => $type,
                'category' => $category,
                'transaction_date' => now()->toDateString(),
                'amount' => $amount,
                'description' => $description,
            ]);

            LogHelper::log(
                'CREATE_FINANCE_WA',
                'Transaksi via WA: '.$category.' Rp'.number_format($amount, 0, ',', '.'),
                $finance,
                ['phone' => $phone]
            );

            $label = $type === 'income' ? 'Pemasukan' : 'Pengeluaran';

            return $label.' *Rp '.number_format($amount, 0, ',', '.').
                "* (kategori: {$category}) berhasil dicatat.\n\n".
                '*Nomor:* '.$finance->uuid."\n".
                'Ketik *MENU* untuk melihat perintah lain.';
        } catch (Throwable $e) {
            LogHelper::logError('CREATE_FINANCE_WA_FAILED', 'Gagal mencatat transaksi via WA', $e, ['phone' => $phone]);

            return 'Maaf, transaksi gagal dicatat. Periksa kembali format ketikkan kamu.';
        }
    }

    private function helpText(): string
    {
        return "Halo, ini *Bot Serrata Kost* 🤖\n\n".
                "Berikut perintah yang bisa dipakai:\n\n".
                "*1. Foto bukti transfer* 📸\n".
                "Kirim foto bukti pembayaran sewa, nanti tercatat sebagai pembayaran *pending*.\n\n".
                "*2. LAPOR <isi laporan>* 🛠️\n".
                "Contoh: LAPOR fasilitas: AC bocor\n".
                "Kategori opsional: facility, cleanliness, security, other.\n\n".
                "*3. FINANCE <tipe> <jumlah> <kategori> <ket>* 💰 (khusus Admin)\n".
                'Contoh: FINANCE expense 50000 Listrik token bulan ini';
    }

    private function unknownText(): string
    {
        return 'Perintah tidak dikenali. Ketik *MENU* untuk melihat daftar perintah.';
    }

    private function isAdmin(string $phone): bool
    {
        $admins = array_map(
            fn ($number) => $this->normalizeNumber((string) $number),
            (array) config('services.whatsapp.admin_phones', [])
        );

        return in_array($phone, $admins, true);
    }

    /**
     * Cari SEMUA tenant yang nomornya cocok dengan pengirim.
     * Bisa lebih dari satu bila satu nomor dipakai beberapa tenant.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Tenant>
     */
    private function findTenants(string $phone): \Illuminate\Database\Eloquent\Collection
    {
        return Tenant::whereIn('phone', $this->phoneVariants($phone))
            ->with('room')
            ->get();
    }

    private function tenantDuePeriod(Tenant $tenant): Carbon
    {
        $due = $tenant->calculated_due_date;

        return $due ? Carbon::parse($due)->startOfMonth() : now()->startOfMonth();
    }

    private function tenantName(Tenant $tenant): string
    {
        return $tenant->name ?: 'Penghuni';
    }

    private function normalizeNumber(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) < 9) {
            return $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        return '62'.$digits;
    }

    private function phoneVariants(string $phone): array
    {
        $digits = preg_replace('/\D/', '', $phone);
        $variants = [$digits, '+'.$digits];

        if (str_starts_with($digits, '62')) {
            $variants[] = '0'.substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            $variants[] = '62'.substr($digits, 1);
            $variants[] = '6'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            $variants[] = '62'.$digits;
            $variants[] = '0'.$digits;
        }

        return array_values(array_unique(array_filter($variants)));
    }

    private function parseAmount(string $raw): ?int
    {
        $cleaned = str_replace(['.', ','], '', $raw);

        if (! ctype_digit($cleaned)) {
            return null;
        }

        return (int) $cleaned;
    }

    private function extensionFromMime(string $mime): string
    {
        return match ($mime) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/jpeg', 'image/jpg' => 'jpg',
            default => 'jpg',
        };
    }

    private function firstWord(string $text): string
    {
        $word = strtok(trim($text), " \t\n");

        return strtoupper((string) $word);
    }
}