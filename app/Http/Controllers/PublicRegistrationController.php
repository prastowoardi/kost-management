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
        if ($paymentMethod == 'transfer') {
            $paymentInfo = "\n*INFO PEMBAYARAN:* 💳\n" .
                        "Silahkan transfer pembayaran sewa ke:\n" .
                        "Bank Mandiri: 1360014406059\n" .
                        "A/N: Prastowo Ardi Widigdo\n" .
                        "Nominal: *Rp " . number_format($tenant->room->price, 0, ',', '.') . "*\n\n" .
                        "Harap kirimkan bukti transfer ke nomor ini ya.";
        } else {
            $paymentInfo = "\n*INFO PEMBAYARAN:* 💵\n" .
                        "Kamu memilih pembayaran tunai. Silahkan siapkan dana sebesar *Rp " . number_format($tenant->room->price, 0, ',', '.') . "* untuk dibayarkan langsung kepada Admin.";
        }

        $message1 = "Halo {$tenant->name}! Pendaftaran kamu di Serrata Kost BERHASIL. 👋✨\n\n" .
                    "Kamar: *{$tenant->room->room_number}*\n" .
                    "Tgl Masuk: " . date('d M Y', strtotime($tenant->entry_date)) . "\n" .
                    $paymentInfo . "\n\n" .
                    "Mohon tunggu sebentar, kami akan mengirimkan tata tertib kost di bawah ini. 👇";

        $message2 = "Berikut adalah *'Rules of the House'* di Serrata Kost: 📝\n\n" .
                    "1. 🕒 *Jam Malam & Tamu:* Tamu berkunjung maksimal sampai jam 23.00 WIB ya. Demi privasi penghuni lain, mohon tidak membawa tamu lawan jenis ke dalam kamar.\n" .
                    "2. 🛏️ *Info Menginap:* Kalau ada keluarga atau teman yang mau menginap, wajib lapor dan konfirmasi ke admin terlebih dahulu ya.\n" .
                    "3. 🚪 *Keamanan Gerbang:* Mohon selalu tutup kembali dan kunci gerbang setiap kali kamu keluar atau masuk area kost. Keamanan kita tanggung jawab bersama! 🔐\n" .
                    "4. 🚿 *Hemat Air & Listrik:* Matikan lampu, AC, alat elektronik, dan keran air kalau lagi nggak dipakai atau saat keluar kamar ya.\n" .
                    "5. 🤫 *Keep it Quiet:* Di atas jam 21.30, tolong kecilkan volume musik atau suara ngobrol biar teman sebelah bisa istirahat tenang.\n" .
                    "6. 🧼 *Kebersihan:* Kamar adalah istanamu, jadi mohon dijaga kebersihannya. Sampah tolong dibuang ke tempat yang sudah disediakan ya.\n" .
                    "7. 🅿️ *Parkir Rapih:* Parkir kendaraan di slot yang sudah ditentukan agar tidak menghalangi jalan keluar-masuk teman lainnya.\n" .
                    "8. 🚭 *Area Merokok:* Mohon tidak merokok di dalam kamar. Gunakan area terbuka yang sudah tersedia ya.\n" .
                    "9. 🍳 *Dapur Bersama:* Habis masak, jangan lupa langsung dicuci alat masaknya dan bersihkan kembali meja dapurnya.\n" .
                    "10. 🧺 *Jemuran:* Kalau sudah kering segera diambil ya, biar bisa gantian sama penghuni lain dan menghindari barang tertukar/hilang.\n" .
                    "11. 🚫 *Barang Terlarang:* *Dilarang keras membawa narkoba, miras, senjata tajam,* atau hewan peliharaan.\n" .
                    "12. 🆘 *Lapor Kendala:* Ada keran bocor, lampu mati, atau kendala lain? Langsung kabari admin lewat chat nomor ini ya!\n\n" .
                    "Selamat bergabung dan semoga betah di Serrata Kost! 🏠🙌";

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
