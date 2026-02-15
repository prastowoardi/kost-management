<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('room')
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
        
        return view('tenants.index', compact('tenants'));
    }

    public function create()
    {
        $availableRooms = Room::where('status', 'available')->get();
        return view('tenants.create', compact('availableRooms'));
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
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('tenants', 'public');
        }

        $tenant = Tenant::create($validated);
        $tenant->room->update(['status' => 'occupied']);

        $waUrl = $this->sendWelcomeMessage($tenant);

        return redirect()->route('tenants.index')
                    ->with('success', 'Penghuni berhasil ditambahkan.')
                    ->with('wa_url', $waUrl);
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['room', 'payments', 'complaints']);
        return view('tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        $rooms = Room::where('status', 'available')
                    ->orWhere('id', $tenant->room_id)
                    ->get();
        
        return view('tenants.edit', compact('tenant', 'rooms'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'phone' => 'required|string|max:20',
            'id_card' => 'required|string|unique:tenants,id_card,' . $tenant->id,
            'address' => 'required|string',
            'entry_date' => 'required|date',
            'exit_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $oldRoomId = $tenant->room_id;

        if ($request->hasFile('photo')) {
            if ($tenant->photo) {
                Storage::disk('public')->delete($tenant->photo);
            }
            $validated['photo'] = $request->file('photo')->store('tenants', 'public');
        }

        $tenant->update($validated);

        if ($tenant->status == 'inactive') {
            Room::where('id', $tenant->room_id)->update(['status' => 'available']);
        } else {
            if ($oldRoomId != $validated['room_id']) {
                Room::where('id', $oldRoomId)->update(['status' => 'available']);
            }
            Room::where('id', $validated['room_id'])->update(['status' => 'occupied']);
        }

        return redirect()->route('tenants.index')
                ->with('success', 'Data penghuni berhasil diupdate');
    }

    public function destroy(Tenant $tenant)
    {
        if ($tenant->photo) {
            Storage::disk('public')->delete($tenant->photo);
        }

        Room::find($tenant->room_id)->update(['status' => 'available']);
        
        $tenant->delete();

        return redirect()->route('tenants.index')
                        ->with('success', 'Penghuni berhasil dihapus');
    }

    public function updateStatus(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
            'exit_date' => 'required_if:status,inactive|nullable|date'
        ]);

        $tenant->update($validated);

        if ($validated['status'] == 'inactive') {
            Room::find($tenant->room_id)->update(['status' => 'available']);
        }

        return redirect()->back()
                        ->with('success', 'Status penghuni berhasil diupdate');
    }

    private function sendWelcomeMessage($tenant)
    {
        $message = "Halo {$tenant->name}! Selamat datang di Serrata Kost! 👋✨\n\n" .
                    "Terimakasih sudah memilih Serrata Kost. Semoga betah dan nyaman ya tinggal di sini! 😊\n\n" .
                    "*Biar lebih asyik, yuk intip 'Rules of the House' kita:* 📝\n\n" .
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
                    "Sekali lagi, selamat bergabung! Selamat istirahat dan semoga betah di Serrata Kost! 🏠🙌";

        try {
            $response = Http::timeout(10)->post('http://localhost:3000/send-message', [
                'number'  => $tenant->phone,
                'message' => $message
            ]);

            if ($response->successful()) {
                Log::info("Pesan WA Terkirim ke {$tenant->phone}");
            } else {
                Log::error("API Gateway Error: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Gagal koneksi ke WhatsApp Gateway: " . $e->getMessage());
        }

        return "https://wa.me/" . $tenant->phone . "?text=" . urlencode($message);
    }
}