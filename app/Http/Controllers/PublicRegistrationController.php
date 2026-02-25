<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PublicRegistrationController extends Controller
{
    public function index()
    {
        $availableRooms = Room::where('status', 'available')->get();
        return view('public.register', compact('availableRooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants',
            'phone' => 'required|string|max:20',
            'id_card' => 'required|string|unique:tenants',
            'address' => 'required|string',
            'entry_date' => 'required|date',
            'payment_method' => 'required|in:transfer,cash',
            'photo' => 'required|image|max:5120',
            'receipt_file' => 'nullable|image|max:5120',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('tenants', 'public');
        }

        $receiptPath = null;
        if ($request->hasFile('receipt_file')) {
            $receiptPath = $request->file('receipt_file')->store('receipts', 'public');
        }

        $validated['status'] = 'active';
        
        $tenant = Tenant::create($validated);
        $tenant->room->update(['status' => 'occupied']);

        \App\Models\Payment::create([
            'tenant_id'      => $tenant->id,
            'room_id'        => $tenant->room_id,
            'amount'         => $tenant->room->price,
            'late_fee'       => 0,
            'total'          => $tenant->room->price,
            'payment_date'   => now(),
            'status'         => 'pending',
            'payment_method' => $request->payment_method,
            'notes'          => 'Pembayaran pendaftaran awal dari form publik',
            'period_month'   => date('Y-m-01'),
            'receipt_file'   => $receiptPath,
        ]);

        $this->sendWelcomeMessage($tenant, $request->payment_method);

        return redirect()->route('public.register.success');
    }

    private function sendWelcomeMessage($tenant, $paymentMethod)
    {
        $paymentInfo = "";

        if ($paymentMethod != 'transfer') {
            // $paymentInfo = "\n*INFO PEMBAYARAN:* 💳\n" .
            //             "Silahkan transfer pembayaran sewa ke:\n" .
            //             "Bank Mandiri: 1360014406059\n" .
            //             "A/N: Prastowo Ardi Widigdo\n" .
            //             "Nominal: *Rp " . number_format($tenant->room->price, 0, ',', '.') . "*\n\n" .
            //             "Harap kirimkan bukti transfer ke nomor ini ya.";
        } else {
            $paymentInfo = "";
        }

        $message1 = "Halo {$tenant->name}! Pendaftaran kamu di Serrata Kost BERHASIL. 👋✨\n\n" .
                    "Kamar: *{$tenant->room->room_number}*\n" .
                    "Tgl Masuk: " . date('d M Y', strtotime($tenant->entry_date)) . "\n" .
                    $paymentInfo . "\n\n" .
                    "Tunggu sebentar ya, mimin kirimkan tata tertib kost di bawah ini. 👇";

        $message2 = "Ini dia *'Rules of the House'* di Serrata Kost: 📝\n\n" .
                    "📍 UMUM\n" .
                    "1. Jaga ketenangan, kebersihan, dan keamanan lingkungan ya.\n" .
                    "2. Kost kita bebas dari Miras, Narkoba, dan barang terlarang lainnya.\n" .
                    "3. No smoking inside (dilarang merokok di kamar).\n" .
                    "4. Tidak diperbolehkan membawa anabul/hewan peliharaan.\n" .
                    "5. Jaga perilaku asusila demi kenyamanan bersama.\n" .
                    "6. Jaga nama baik Serrata Kost di mana pun kita berada.\n" .
                    "7. Pembayaran kost tepat waktu ya Kak, sesuai tanggal janjian.\n\n" .
                "⏰ JAM KEGIATAN\n" .
                    "1. Jam malam maksimal pukul 22.00 WIB. Kalau terpaksa telat, wajib kabari Ibu Kost ya.\n" .
                    "2. Tamu laki-laki dilarang masuk kamar (ketemu di teras saja).\n" .
                    "3. Ada keluarga mau menginap? Wajib lapor dulu ke Ibu Kost ya (Max. 2 orang).\n\n" .
                "✨ KEBERSIHAN\n" .
                    "1. Jaga kebersihan kamar & kamar mandi masing-masing.\n" .
                    "2. Buang sampah pada tempatnya (jangan buang sampah/pembalut di kloset ya, be gentle with the toilet!).\n" .
                    "3. Jemur pakaian di tempat jemuran yang tersedia, jangan di depan kamar ya Kak.\n\n" .
                "🚰 FASILITAS\n" .
                    "1. Gunakan fasilitas kost dengan bijak & hemat air.\n" .
                    "2. Jika ada kerusakan karena kelalaian, biaya perbaikan ditanggung penghuni ya.\n\n" .

                    "Sekali lagi, selamat bergabung! Selamat istirahat dan semoga betah di Serrata Kost! 🏠💖";

        try {
            Http::timeout(10)->post('http://localhost:3000/send-message', [
                'number'  => $tenant->phone,
                'message' => $message1
            ]);

            sleep(1);

            Http::timeout(10)->post('http://localhost:3000/send-message', [
                'number'  => $tenant->phone,
                'message' => $message2
            ]);

        } catch (\Exception $e) {
            Log::error("WA Error: " . $e->getMessage());
        }
    }

    public function success()
    {
        return view('public.register_success');
    }
}
